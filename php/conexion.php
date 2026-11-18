<?php
// Datos de conexión (en XAMPP el usuario root no tiene contraseña por defecto)
$servidor = "localhost";
$usuarioBD = "root";
$claveBD = "123";
$nombreBD = "saberplus";

// Hace que mysqli lance excepciones cuando hay errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli($servidor, $usuarioBD, $claveBD, $nombreBD);
    $conexion->set_charset("utf8mb4");
} catch (mysqli_sql_exception $error) {
    // No mostramos el error real al usuario por seguridad
    die("No se pudo conectar con la base de datos. Revisa que MySQL esté encendido y que importaste bd/saberplus.sql.");
}
