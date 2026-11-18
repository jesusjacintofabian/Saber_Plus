<?php
session_start();
require "conexion.php";
require "funciones.php";
verificarAdmin();

$areas = $conexion->query("SELECT idArea, nombre FROM areas ORDER BY idArea")->fetch_all(MYSQLI_ASSOC);
$niveles = ["Principiante", "Intermedio", "Avanzado"];

$errores = [];
$mensaje = "";
$datos = ["idArea" => 0, "nombre" => "", "proveedor" => "", "nivel" => "", "descripcion" => "", "precio" => "", "precioAnterior" => "", "urlOficial" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim($_POST[$campo] ?? "");
    }
    $datos["idArea"] = (int) $datos["idArea"];

    if (!validarCsrf()) {
        $errores[] = "La sesión expiró, intenta de nuevo.";
    }
    if ($datos["idArea"] < 1 || $datos["idArea"] > 8) {
        $errores[] = "Selecciona un área.";
    }
    if ($datos["nombre"] == "" || strlen($datos["nombre"]) > 120) {
        $errores[] = "El nombre es obligatorio (máximo 120 caracteres).";
    }
    if ($datos["proveedor"] == "" || strlen($datos["proveedor"]) > 80) {
        $errores[] = "El proveedor es obligatorio (máximo 80 caracteres).";
    }
    if (!in_array($datos["nivel"], $niveles)) {
        $errores[] = "Selecciona un nivel.";
    }
    if (strlen($datos["descripcion"]) > 255) {
        $errores[] = "La descripción no puede pasar de 255 caracteres.";
    }
    if (!is_numeric($datos["precio"]) || $datos["precio"] < 0) {
        $errores[] = "El precio debe ser un número mayor o igual a 0.";
    }
    if ($datos["precioAnterior"] != "" && (!is_numeric($datos["precioAnterior"]) || $datos["precioAnterior"] < 0)) {
        $errores[] = "El precio anterior debe ser un número.";
    }
    if ($datos["urlOficial"] != "" && !filter_var($datos["urlOficial"], FILTER_VALIDATE_URL)) {
        $errores[] = "La URL oficial no es válida.";
    }

    if (count($errores) == 0) {
        // El código se arma con el nombre en camelCase, igual que los id de las páginas
        $palabras = preg_split('/[^A-Za-z0-9]+/', iconv("UTF-8", "ASCII//TRANSLIT", $datos["nombre"]), -1, PREG_SPLIT_NO_EMPTY);
        $codigo = "curso" . implode("", array_map(fn($p) => ucfirst(strtolower($p)), $palabras));
        $codigo = substr($codigo, 0, 70) . rand(100, 999);

        $precio = (float) $datos["precio"];
        $precioAnterior = $datos["precioAnterior"] == "" ? null : (float) $datos["precioAnterior"];

        $stmt = $conexion->prepare("INSERT INTO cursos (codigo, idArea, nombre, proveedor, nivel, descripcion, precio, precioAnterior, urlOficial)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissssdds", $codigo, $datos["idArea"], $datos["nombre"], $datos["proveedor"], $datos["nivel"],
                          $datos["descripcion"], $precio, $precioAnterior, $datos["urlOficial"]);
        $stmt->execute();
        $stmt->close();

        $mensaje = "Curso «" . $datos["nombre"] . "» agregado. Ya aparece en la página Buscar.";
        $datos = ["idArea" => 0, "nombre" => "", "proveedor" => "", "nivel" => "", "descripcion" => "", "precio" => "", "precioAnterior" => "", "urlOficial" => ""];
    }
}

$tituloPagina = "Agregar curso";
$subtituloPagina = "Registra un curso nuevo en la base de datos.";
$colorTema = "#3b4a63";
$colorTemaOscuro = "#232c3b";
$paginaActual = "admin";
require "encabezado.php";
?>
<a class="enlaceVolver" href="panelAdmin.php"><i class="bi bi-arrow-left"></i> Volver al panel</a>
<div class="d-flex justify-content-center">
  <form action="agregarCurso.php" method="post" class="formularioSugerencias">
    <?php if ($mensaje != ""): ?><div class="alert alert-success"><?= e($mensaje) ?></div><?php endif; ?>
    <?php if (count($errores) > 0): ?>
      <div class="alert alert-danger"><?php foreach ($errores as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div>
    <?php endif; ?>
    <input type="hidden" name="token" value="<?= tokenCsrf() ?>">
    <div class="row g-3">
      <div class="col-md-6">
        <label for="idArea" class="form-label">Área</label>
        <select class="form-select" id="idArea" name="idArea" required>
          <option value="">Selecciona un área</option>
          <?php foreach ($areas as $area): ?>
            <option value="<?= $area["idArea"] ?>" <?= $area["idArea"] == $datos["idArea"] ? "selected" : "" ?>><?= e($area["nombre"]) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label for="nivel" class="form-label">Nivel</label>
        <select class="form-select" id="nivel" name="nivel" required>
          <option value="">Selecciona un nivel</option>
          <?php foreach ($niveles as $nivel): ?>
            <option <?= $nivel == $datos["nivel"] ? "selected" : "" ?>><?= $nivel ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre del curso</label>
        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="120" value="<?= e($datos["nombre"]) ?>" required>
      </div>
      <div class="col-md-6">
        <label for="proveedor" class="form-label">Proveedor</label>
        <input type="text" class="form-control" id="proveedor" name="proveedor" maxlength="80" value="<?= e($datos["proveedor"]) ?>" required>
      </div>
      <div class="col-12">
        <label for="descripcion" class="form-label">Descripción</label>
        <input type="text" class="form-control" id="descripcion" name="descripcion" maxlength="255" value="<?= e($datos["descripcion"]) ?>">
      </div>
      <div class="col-md-6">
        <label for="precio" class="form-label">Precio ($)</label>
        <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01" value="<?= e($datos["precio"]) ?>" required>
      </div>
      <div class="col-md-6">
        <label for="precioAnterior" class="form-label">Precio anterior ($, opcional)</label>
        <input type="number" class="form-control" id="precioAnterior" name="precioAnterior" min="0" step="0.01" value="<?= e($datos["precioAnterior"]) ?>">
      </div>
      <div class="col-12">
        <label for="urlOficial" class="form-label">URL oficial (opcional)</label>
        <input type="url" class="form-control" id="urlOficial" name="urlOficial" maxlength="255" value="<?= e($datos["urlOficial"]) ?>">
      </div>
    </div>
    <button type="submit" class="btn btnOficial btn-lg mt-3">Guardar curso</button>
  </form>
</div>
<?php require "pie.php"; ?>
