<?php
// Script de depuración para ver qué productos están en la base de datos
require_once 'config.php';

echo "<h2>Productos en la base de datos:</h2>";

try {
    $stmt = $pdo->query("SELECT id, nombre, codigo, stock_actual FROM products ORDER BY id");
    $products = $stmt->fetchAll();
    
    if (count($products) == 0) {
        echo "<p><strong>¡No hay productos en la base de datos!</strong></p>";
        echo "<p>Necesitas:</p>";
        echo "<ol>";
        echo "<li>Crear la base de datos 'inventario_hdlc'</li>";
        echo "<li>Ejecutar el script SQL de database.sql</li>";
        echo "<li>O agregar productos manualmente</li>";
        echo "</ol>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Código de Barras</th><th>Stock</th></tr>";
        
        foreach($products as $product) {
            echo "<tr>";
            echo "<td>" . $product['id'] . "</td>";
            echo "<td>" . $product['nombre'] . "</td>";
            echo "<td>" . ($product['codigo'] ? $product['codigo'] : '<em>Sin código</em>') . "</td>";
            echo "<td>" . $product['stock_actual'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Códigos de barras disponibles:</h3>";
        echo "<ul>";
        foreach($products as $product) {
            if ($product['codigo']) {
                echo "<li><strong>" . $product['codigo'] . "</strong> - " . $product['nombre'] . "</li>";
            }
        }
        echo "</ul>";
    }
    
} catch(PDOException $e) {
    echo "<p><strong>Error de conexión:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Asegúrate de que:</p>";
    echo "<ol>";
    echo "<li>Laragon esté ejecutándose</li>";
    echo "<li>La base de datos 'inventario_hdlc' exista</li>";
    echo "<li>Las tablas estén creadas</li>";
    echo "</ol>";
}
?>

<h3>Instrucciones:</h3>
<ol>
    <li>Ve a <a href="http://localhost/phpmyadmin">phpMyAdmin</a></li>
    <li>Crea la base de datos "inventario_hdlc" si no existe</li>
    <li>Ejecuta el contenido del archivo database.sql</li>
    <li>Regresa a esta página para verificar que los productos aparezcan</li>
    <li>Luego prueba el escáner con los códigos mostrados arriba</li>
</ol>
