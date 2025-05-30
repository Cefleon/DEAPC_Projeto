<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
date_default_timezone_set('Europe/Lisbon');


if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require('fpdf/fpdf.php');


class PDF extends FPDF
{
    public $title = 'Relatório';
    public $username = 'Usuário';
    public $companyName = 'DEAPC INVENTORY';
    public $logoPath = 'images/logo.jpg';

    function Header()
    {
        if (file_exists($this->logoPath)) {
            $this->Image($this->logoPath, 10, 6, 30);
        }

        $this->SetFont('Arial', 'B', 14);
        $this->SetXY(45, 10);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $this->companyName), 0, 1);

        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $this->title), 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $dateUser = date('d/m/Y H:i') . ' | ' . $this->username;
        $this->SetXY(150, 30);
        $this->Cell(50, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $dateUser), 0, 1, 'R');

        $this->Line(10, 40, $this->GetPageWidth() - 10, 40);
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);

        $page = 'Página ' . $this->PageNo() . '/{nb}';
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $page), 0, 0, 'L');

        $copyright = '© ' . date('Y') . ' ' . $this->companyName;
        $this->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $copyright), 0, 0, 'R');
    }
}


$db = new SQLite3(__DIR__ . '/inventory.db');

// Obter os dados do formulário
$reportType = $_POST['report-type'] ?? 'stock';
$format = $_POST['format'] ?? 'csv';
$dateStart = $_POST['date-start'] ?? null;
$dateEnd = $_POST['date-end'] ?? null;

// Função para ir à base de dados e buscar os dados necessários
function fetchData($db, $reportType, $dateStart, $dateEnd) {
    $data = [];
    $where = "";

    $hasDate = in_array($reportType, ['orders']); // só orders tem data
    if ($hasDate && $dateStart && $dateEnd) {
        $where = " WHERE data >= '$dateStart' AND data <= '$dateEnd'";
    }

    switch ($reportType) {
        case 'stock':
            $query = "SELECT id, nome_produto, quantidade, categoria FROM stock";
            break;
        case 'orders':
            $query = "SELECT id, cliente, produto, quantidade, data FROM encomendas $where";
            break;
        case 'users':
            $where = "";
            if ($dateStart && $dateEnd) {
                $where = " WHERE created_at >= '$dateStart' AND created_at <= '$dateEnd'";
            }
            $query = "SELECT id, username, email, role, created_at FROM users $where";
            break;
        default:
            return [];
    }

    $results = $db->query($query);

    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
    }

    return $data;
}

$data = fetchData($db, $reportType, $dateStart, $dateEnd);

// Cabeçalhos dinâmicos
$titles = [
    'stock'  => 'Relatório: Stock',
    'orders' => 'Relatório: Encomendas',
    'users'  => 'Relatório: Utilizadores'
];

// Gerar o CSV
if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio_' . $reportType . '.csv"');

    $output = fopen('php://output', 'w');

    if (!empty($data)) {
        fputcsv($output, array_keys($data[0]));
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    } else {
        fputcsv($output, ['Nenhum dado encontrado']);
    }

    fclose($output);
    exit();
}

// Gerar o PDF
if ($format === 'pdf') {
    $pdf = new PDF();
    $pdf->title = $titles[$reportType] ?? 'Relatório';
    $pdf->username = $_SESSION['username'];
    $pdf->companyName = 'DEAPC INVENTORY';
    $pdf->logoPath = 'images/logo.jpg';

    $pdf->AliasNbPages();
    $pdf->AddPage();

    if (!empty($data)) {
        $headers = array_keys($data[0]);
        $columnCount = count($headers);
        
        $pageWidth = $pdf->GetPageWidth() - 20; 
        $columnWidth = $pageWidth / $columnCount;

        // Cabeçalho da tabela com fundo cinza
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 12);
        foreach ($headers as $header) {
            $pdf->Cell($columnWidth, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $header), 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Dados da tabela
        $pdf->SetFont('Arial', '', 10);
        $fill = false;
        foreach ($data as $row) {
            foreach ($row as $cell) {
                $pdf->SetFillColor($fill ? 235 : 255, $fill ? 235 : 255, $fill ? 235 : 255);
                $pdf->Cell($columnWidth, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', strval($cell)), 1, 0, 'L', true);
            }
            $pdf->Ln();
            $fill = !$fill;
        }
    } else {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Nenhum dado encontrado.'), 0, 1);
    }

    if (ob_get_length()) {
        ob_clean();
    }

    $pdf->Output('D', "relatorio_$reportType.pdf");
    exit();
}
