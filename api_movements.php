<?php
// API para gestión de movimientos de inventario
require_once 'config.php';

// Configurar headers para la API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($method) {
    case 'GET':
        if($action == 'all') {
            getAllMovements();  // Obtener todos los movimientos
        }
        break;
    case 'POST':
        if($action == 'add') {
            addMovement();  // Agregar un nuevo movimiento
        }
        break;
}

// Función para obtener todos los movimientos
function getAllMovements() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM movements ORDER BY fecha DESC");
        $movements = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $movements]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

// Función para agregar un nuevo movimiento y actualizar el stock
function addMovement() {
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        // Iniciar transacción para asegurar consistencia de datos
        $pdo->beginTransaction();
        
        // Primero, obtener el producto actual
        $stmtProduct = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmtProduct->execute([$data['producto_id']]);
        $product = $stmtProduct->fetch();
        
        if (!$product) {
            throw new Exception("Producto no encontrado");
        }
        
        // Calcular el nuevo stock según el tipo de movimiento
        $newStock = $product['stock_actual'];
        if ($data['tipo'] === 'entry') {
            $newStock += $data['cantidad'];  // Sumar en caso de entrada
        } else {
            if ($newStock < $data['cantidad']) {
                throw new Exception("Stock insuficiente para este movimiento");
            }
            $newStock -= $data['cantidad'];  // Restar en caso de salida
        }
        
        // Actualizar el stock del producto
        $stmtUpdateStock = $pdo->prepare("UPDATE products SET stock_actual = ? WHERE id = ?");
        $stmtUpdateStock->execute([$newStock, $data['producto_id']]);
        
        // Insertar el registro del movimiento
        $stmtMovement = $pdo->prepare("INSERT INTO movements (producto_id, tipo, producto, cantidad, motivo, usuario, fecha) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmtMovement->execute([
            $data['producto_id'],
            $data['tipo'],
            $data['producto'],
            $data['cantidad'],
            $data['motivo'],
            $data['usuario']
        ]);
        
        $movementId = $pdo->lastInsertId();
        
        // Confirmar la transacción
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'id' => $movementId, 
            'new_stock' => $newStock
        ]);
        
    } catch(Exception $e) {
        // Deshacer la transacción en caso de error
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } catch(PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
