<?php
// Configuración de la base de datos MySQL
$host = 'localhost';           // Servidor de la base de datos (localhost para Laragon)
$username = 'root';            // Usuario de MySQL (por defecto 'root' en Laragon)
$password = '';                // Contraseña de MySQL (vacía por defecto en Laragon)
$database = 'inventario_hdlc'; // Nombre de la base de datos

try {
    // Crear conexión a la base de datos usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    
    // Configurar PDO para que muestre errores si algo falla
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar PDO para que devuelva arrays asociativos (con nombres de columnas)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Si no se puede conectar, mostrar error y detener el script
    die("Error de conexión: " . $e->getMessage());
}
?>
