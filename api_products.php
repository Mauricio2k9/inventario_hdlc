<?php
// Incluir la configuración de la base de datos
require_once 'config.php';

// Configurar headers para que la API funcione correctamente
header('Content-Type: application/json');                    // Respuestas en formato JSON
header('Access-Control-Allow-Origin: *');                   // Permitir acceso desde cualquier origen
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); // Métodos HTTP permitidos
header('Access-Control-Allow-Headers: Content-Type');       // Headers permitidos

// Obtener el método HTTP (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];
// Obtener la acción solicitada desde la URL (?action=all, ?action=single, etc.)
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Switch para manejar diferentes métodos HTTP
switch($method) {
    case 'GET':
        // Métodos para obtener datos
        if($action == 'all') {
            getAllProducts();  // Obtener todos los productos
        } elseif($action == 'single' && isset($_GET['id'])) {
            getProduct($_GET['id']);  // Obtener un producto específico por ID
        }
        break;
    case 'POST':
        // Métodos para crear nuevos registros
        if($action == 'add') {
            addProduct();  // Agregar un nuevo producto
        }
        break;
    case 'PUT':
        // Métodos para actualizar registros existentes
        if($action == 'update') {
            updateProduct();  // Actualizar un producto existente
        }
        break;
    case 'DELETE':
        // Métodos para eliminar registros
        if($action == 'delete' && isset($_GET['id'])) {
            deleteProduct($_GET['id']);  // Eliminar un producto por ID
        }
        break;
}

// Función para obtener todos los productos con información del proveedor
function getAllProducts() {
    global $pdo;  // Usar la conexión global a la base de datos
    try {
        // Consulta SQL que une productos con proveedores para obtener el nombre del proveedor
        $stmt = $pdo->query("SELECT p.*, s.nombre as proveedor_nombre FROM products p LEFT JOIN suppliers s ON p.proveedor_id = s.id ORDER BY p.id DESC");
        $products = $stmt->fetchAll();  // Obtener todos los resultados
        
        // Respuesta exitosa con los datos
        echo json_encode(['success' => true, 'data' => $products]);
    } catch(PDOException $e) {
        // En caso de error, devolver mensaje de error
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Función para obtener un producto específico por ID
function getProduct($id) {
    global $pdo;
    try {
        // Consulta preparada para evitar inyección SQL
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);  // Ejecutar con el ID proporcionado
        $product = $stmt->fetch();  // Obtener un solo resultado
        
        // Respuesta con el producto encontrado
        echo json_encode(['success' => true, 'data' => $product]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Función para agregar un nuevo producto
function addProduct() {
    global $pdo;
    // Obtener datos JSON enviados desde el frontend
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        // Insertar nuevo producto en la base de datos
        $stmt = $pdo->prepare("INSERT INTO products (nombre, codigo, descripcion, categoria, proveedor_id, stock_actual, stock_minimo, precio, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $data['nombre'],        // Nombre del producto
            $data['codigo'],        // Código de barras
            $data['descripcion'],   // Descripción
            $data['categoria'],     // Categoría
            $data['proveedor_id'],  // ID del proveedor
            $data['stock_actual'],  // Cantidad en stock
            $data['stock_minimo'],  // Stock mínimo
            $data['precio']         // Precio unitario
        ]);
        
        // Obtener el ID del producto recién creado
        $productId = $pdo->lastInsertId();
        
        // Si el stock inicial es mayor a 0, crear un movimiento de entrada
        if($data['stock_actual'] > 0) {
            $stmtMove = $pdo->prepare("INSERT INTO movements (producto_id, tipo, producto, cantidad, motivo, usuario, fecha) VALUES (?, 'entry', ?, ?, 'Stock inicial', 'admin', NOW())");
            $stmtMove->execute([$productId, $data['nombre'], $data['stock_actual']]);
        }
        
        // Respuesta exitosa con el ID del nuevo producto
        echo json_encode(['success' => true, 'id' => $productId]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Función para actualizar un producto existente
function updateProduct() {
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        // Actualizar los datos del producto
        $stmt = $pdo->prepare("UPDATE products SET nombre=?, codigo=?, descripcion=?, categoria=?, proveedor_id=?, stock_actual=?, stock_minimo=?, precio=? WHERE id=?");
        $stmt->execute([
            $data['nombre'],
            $data['codigo'],
            $data['descripcion'],
            $data['categoria'],
            $data['proveedor_id'],
            $data['stock_actual'],
            $data['stock_minimo'],
            $data['precio'],
            $data['id']  // ID del producto a actualizar
        ]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Función para eliminar un producto
function deleteProduct($id) {
    global $pdo;
    try {
        // Eliminar el producto por ID
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
