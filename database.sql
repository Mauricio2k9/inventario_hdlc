-- Create database
CREATE DATABASE IF NOT EXISTS inventario_hdlc;
USE inventario_hdlc;

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    codigo VARCHAR(100),
    descripcion TEXT,
    categoria VARCHAR(100) NOT NULL,
    proveedor_id INT,
    stock_actual INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 5,
    precio DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create suppliers table
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    contacto VARCHAR(255),
    telefono VARCHAR(50),
    email VARCHAR(255),
    direccion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create movements table
CREATE TABLE IF NOT EXISTS movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    tipo ENUM('entry', 'exit') NOT NULL,
    producto VARCHAR(255) NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(255),
    usuario VARCHAR(100) DEFAULT 'admin',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert initial suppliers
INSERT INTO suppliers (nombre, contacto, telefono, email, direccion) VALUES
('TecnoSuministros', 'Juan Pérez', '555-1234', 'ventas@tecnosuministros.com', 'Av. Tecnológica 123, Ciudad');

-- Insert initial products
INSERT INTO products (nombre, codigo, descripcion, categoria, proveedor_id, stock_actual, stock_minimo, precio) VALUES
('Laptop HP EliteBook', '123456789012', 'Laptop empresarial con procesador i7 y 16GB RAM', 'Computadoras', 1, 15, 5, 1299.99),
('Teclado inalámbrico Logitech', '987654321098', '', 'Periféricos', 1, 32, 10, 59.99);

-- Insert initial movements
INSERT INTO movements (producto_id, tipo, producto, cantidad, motivo, usuario) VALUES
(1, 'entry', 'Laptop HP EliteBook', 20, 'Compra inicial', 'admin'),
(2, 'entry', 'Teclado inalámbrico Logitech', 35, 'Compra inicial', 'admin');
