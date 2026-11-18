<?php
session_start();
require "conexion.php";
require "funciones.php";

// Solo se acepta el formulario enviado por POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../html/contacto.html");
    exit;
}

// Recibir y limpiar los datos
$nombre = trim($_POST["nombreUsuario"] ?? "");
$correo = trim($_POST["correoUsuario"] ?? "");
$idArea = (int) ($_POST["areaTecnologica"] ?? 0);
$tipo = $_POST["tipoSugerencia"] ?? "";
$nombreSugerencia = trim($_POST["nombreSugerencia"] ?? "");
$motivo = trim($_POST["motivoSugerencia"] ?? "");
$comentarios = trim($_POST["comentarios"] ?? "");

// Validaciones en el servidor (no basta con el "required" del HTML)
$errores = [];
if ($nombre == "" || strlen($nombre) > 100) {
    $errores[] = "Escribe tu nombre (máximo 100 caracteres).";
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 120) {
    $errores[] = "El correo electrónico no es válido.";
}
if ($idArea < 1 || $idArea > 8) {
    $errores[] = "Selecciona un área tecnológica.";
}
if ($tipo != "curso" && $tipo != "certificacion") {
    $errores[] = "Indica si es un curso o una certificación.";
}
if ($nombreSugerencia == "" || strlen($nombreSugerencia) > 150) {
    $errores[] = "Escribe el nombre del curso o certificación.";
}
if ($motivo == "" || strlen($motivo) > 255) {
    $errores[] = "Escribe el motivo de la sugerencia (máximo 255 caracteres).";
}
if (strlen($comentarios) > 2000) {
    $errores[] = "Los comentarios son demasiado largos.";
}

// Si todo está bien se guarda con el procedimiento almacenado
if (count($errores) == 0) {
    $stmt = $conexion->prepare("CALL sp_guardarContacto(?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissss", $nombre, $correo, $idArea, $tipo, $nombreSugerencia, $motivo, $comentarios);
    $stmt->execute();
    $stmt->close();
}

$tituloPagina = "Contacto / Sugerencias";
$subtituloPagina = "";
$colorTema = "#7a2e5a";
$colorTemaOscuro = "#4d1c38";
$paginaActual = "";
require "encabezado.php";
?>
<div class="d-flex justify-content-center">
  <div class="formularioSugerencias text-center">
    <?php if (count($errores) == 0): ?>
      <i class="bi bi-check-circle-fill text-success display-4"></i>
      <h2 class="h4 mt-3">¡Gracias, <?= e($nombre) ?>!</h2>
      <p>Tu sugerencia <strong>«<?= e($nombreSugerencia) ?>»</strong> fue guardada. La revisaremos pronto.</p>
      <a class="btn btnOficial" href="../html/index.html">Volver al inicio</a>
    <?php else: ?>
      <i class="bi bi-exclamation-triangle-fill text-danger display-4"></i>
      <h2 class="h4 mt-3">No se pudo enviar</h2>
      <ul class="text-start">
        <?php foreach ($errores as $error): ?>
          <li><?= e($error) ?></li>
        <?php endforeach; ?>
      </ul>
      <a class="btn btnOficial" href="../html/contacto.html">Volver al formulario</a>
    <?php endif; ?>
  </div>
</div>
<?php require "pie.php"; ?>
