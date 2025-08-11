<?php
// Incluir la configuración de la base de datos
require_once 'config.php';

// Configurar headers para la API
header('Content-Type: application/json');                    // Respuestas en formato JSON
header('Access-Control-Allow-Origin: *');                   // Permitir acceso desde cualquier origen
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE'); // Métodos HTTP permitidos
header('Access-Control-Allow-Headers: Content-Type');       // Headers permitidos

// Obtener el método HTTP y la acción solicitada
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Manejar las diferentes peticiones
switch($method) {
    case 'GET':
        if($action == 'all') {
            getAllSuppliers();  // Obtener todos los proveedores
        }
        break;
}

// Función para obtener todos los proveedores ordenados por nombre
function getAllSuppliers() {
    global $pdo;  // Usar la conexión global a la base de datos
    try {
        // Consulta para obtener todos los proveedores ordenados alfabéticamente
        $stmt = $pdo->query("SELECT * FROM suppliers ORDER BY nombre");
        $suppliers = $stmt->fetchAll();  // Obtener todos los resultados
        
        // Respuesta exitosa con la lista de proveedores
        echo json_encode(['success' => true, 'data' => $suppliers]);
    } catch(PDOException $e) {
        // En caso de error, devolver mensaje de error
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
