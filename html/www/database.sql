CREATE DATABASE deapc_inventory;

USE deapc_inventory;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'supplier') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE deliveries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL,
    user_id INT NOT NULL,
    delivery_date DATETIME NOT NULL,
    status ENUM('in_progress', 'completed') DEFAULT 'in_progress',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE delivery_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    delivery_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    delivery_date DATE NOT NULL,
    FOREIGN KEY (delivery_id) REFERENCES deliveries(id) ON DELETE CASCADE
);

-- Inserir um usuário de teste (senha: 123456)
INSERT INTO users (name, email, password, role) 
VALUES ('Fornecedor Teste', 'fornecedor@teste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supplier');