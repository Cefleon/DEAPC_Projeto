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

$reportType = $_POST['report-type'] ?? 'stock';
$format = $_POST['format'] ?? 'csv';
$month = $_POST['month'] ?? '';

$titles = [
    'stock'  => 'Relatório: Stock',
    'orders' => 'Relatório: Encomendas',
    'users'  => 'Relatório: Utilizadores'
];

$headersCustom = [
    'stock'  => ['ID', 'Produto', 'Quantidade', 'Data de Atualização'],
    'orders' => ['ID', 'Empresa', 'Utilizador', 'Data de Entrega', 'Estado', 'Quantidade', 'Tipo'],
    'users'  => ['ID', 'Nome', 'Email', 'Perfil', 'Criado em']
];

$map = [
    // Stock
    'ID' => 'ID',
    'Produto' => 'Produto',
    'Quantidade' => 'Quantidade',
    'Data de Atualização' => 'Data_Atualização',
    // Orders
    'ID' => 'id',
    'Empresa' => 'company_name',
    'Utilizador' => 'user_id',
    'Data de Entrega' => 'delivery_date',
    'Estado' => 'status',
    'Quantidade' => 'quantity',
    'Tipo' => 'Tipo',
    // Users
    'Nome' => 'name',
    'Email' => 'email',
    'Perfil' => 'role',
    'Criado em' => 'created_at'
];

function fetchData($db, $reportType, $month)
{
    $data = [];
    $where = "";

    if ($reportType === 'stock') {
        $query = "SELECT ID, Produto, Quantidade, Data_Atualização FROM AtualizaStock";
        if ($month) {
            $where = " WHERE strftime('%m', Data_Atualização) = '$month'";
            $query .= $where;
        }
    } elseif ($reportType === 'orders') {
        $query = "SELECT id, company_name, user_id, delivery_date, status, quantity, Tipo FROM deliveries";
        if ($month) {
            $where = " WHERE strftime('%m', delivery_date) = '$month'";
            $query .= $where;
        }
    } elseif ($reportType === 'users') {
        $query = "SELECT id, name, email, role, created_at FROM users";
        if ($month) {
            $where = " WHERE strftime('%m', created_at) = '$month'";
            $query .= $where;
        }
    } else {
        return [];
    }

    $results = $db->query($query);
    if (!$results) {
        return [];
    }

    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $data[] = $row;
    }

    return $data;
}

$data = fetchData($db, $reportType, $month);

$meses = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
    '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
    '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];

if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio_' . $reportType . '.csv"');

    $output = fopen('php://output', 'w');

    // Título do relatório
    fputcsv($output, [$titles[$reportType] ?? 'Relatório']);

    // Mês selecionado
    if ($month && isset($meses[$month])) {
        fputcsv($output, ["Mês: " . $meses[$month]]);
    }

    // Cabeçalhos personalizados
    $headers = $headersCustom[$reportType] ?? array_keys($data[0] ?? []);
    fputcsv($output, $headers);

    if (!empty($data)) {
        foreach ($data as $row) {
            $orderedRow = [];
            foreach ($headers as $h) {
                $key = $map[$h] ?? $h;
                $orderedRow[] = $row[$key] ?? '';
            }
            fputcsv($output, $orderedRow);
        }
    } else {
        fputcsv($output, ['Nenhum dado encontrado']);
    }

    fclose($output);
    exit();
}

if ($format === 'pdf') {
    $pdf = new PDF();
    $pdf->title = $titles[$reportType] ?? 'Relatório';
    $pdf->username = $_SESSION['username'];
    $pdf->companyName = 'DEAPC INVENTORY';
    $pdf->logoPath = 'images/logo.jpg';

    $pdf->AliasNbPages();
    $pdf->AddPage();

    // Mês selecionado
    if ($month && isset($meses[$month])) {
        $pdf->SetFont('Arial', 'I', 11);
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Mês: " . $meses[$month]), 0, 1, 'C');
        $pdf->Ln(2);
    }

    $headers = $headersCustom[$reportType] ?? array_keys($data[0] ?? []);
    $columnCount = count($headers);
    $pageWidth = $pdf->GetPageWidth() - 20;

    // Calcular larguras automáticas
    $maxWidths = array_fill(0, $columnCount, 0);
    $pdf->SetFont('Arial', 'B', 12);
    foreach ($headers as $i => $header) {
        $w = $pdf->GetStringWidth($header) + 8;
        $maxWidths[$i] = max($maxWidths[$i], $w);
    }
    $pdf->SetFont('Arial', '', 10);
    foreach ($data as $row) {
        foreach ($headers as $i => $h) {
            $key = $map[$h] ?? $h;
            $value = isset($row[$key]) ? $row[$key] : '';
            $w = $pdf->GetStringWidth($value) + 8;
            $maxWidths[$i] = max($maxWidths[$i], $w);
        }
    }
    // Ajustar para não ultrapassar a página
    $totalWidth = array_sum($maxWidths);
    if ($totalWidth > $pageWidth) {
        $factor = $pageWidth / $totalWidth;
        foreach ($maxWidths as $i => $w) {
            $maxWidths[$i] = $w * $factor;
        }
    }

    // Cabeçalhos
    $pdf->SetFillColor(220, 220, 220);
    $pdf->SetFont('Arial', 'B', 12);
    foreach ($headers as $i => $header) {
        $pdf->Cell($maxWidths[$i], 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $header), 1, 0, 'C', true);
    }
    $pdf->Ln();

    // Dados
    $pdf->SetFont('Arial', '', 10);
    $fill = false;
    if (!empty($data)) {
        foreach ($data as $row) {
            foreach ($headers as $i => $h) {
                $key = $map[$h] ?? $h;
                $value = isset($row[$key]) ? $row[$key] : '';
                $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
                $pdf->Cell($maxWidths[$i], 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', strval($value)), 1, 0, 'L', true);
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
