<?php
session_start();
require "conexion.php";
require "funciones.php";

// Filtros que llegan por GET
$texto = trim($_GET["texto"] ?? "");
$idArea = (int) ($_GET["area"] ?? 0);
if (strlen($texto) > 100) {
    $texto = substr($texto, 0, 100);
}

// Lista de áreas para el select
$areas = $conexion->query("SELECT idArea, nombre FROM areas ORDER BY idArea")->fetch_all(MYSQLI_ASSOC);

// Consulta con el procedimiento almacenado
$stmt = $conexion->prepare("CALL sp_buscarCursos(?, ?)");
$stmt->bind_param("si", $texto, $idArea);
$stmt->execute();
$cursos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$tituloPagina = "Buscar cursos";
$subtituloPagina = "Consulta los cursos guardados en la base de datos por nombre, proveedor o área.";
$colorTema = "#0f7a8a";
$colorTemaOscuro = "#0a525d";
$paginaActual = "buscar";
require "encabezado.php";
?>

<form action="buscarCursos.php" method="get" class="tarjetaPes p-3 mb-4">
  <div class="row g-2 align-items-end">
    <div class="col-md-6">
      <label for="texto" class="form-label">Nombre o proveedor</label>
      <input type="text" class="form-control" id="texto" name="texto" maxlength="100" value="<?= e($texto) ?>" placeholder="Ej: Python, Cisco, SQL">
    </div>
    <div class="col-md-4">
      <label for="area" class="form-label">Área</label>
      <select class="form-select" id="area" name="area">
        <option value="0">Todas las áreas</option>
        <?php foreach ($areas as $area): ?>
          <option value="<?= $area["idArea"] ?>" <?= $area["idArea"] == $idArea ? "selected" : "" ?>><?= e($area["nombre"]) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2 d-grid">
      <button type="submit" class="btn btnOficial"><i class="bi bi-search"></i> Buscar</button>
    </div>
  </div>
</form>

<p class="textoSuave"><?= count($cursos) ?> curso(s) encontrado(s).</p>

<div class="row g-3">
  <?php foreach ($cursos as $curso): ?>
  <div class="col-md-6 col-xl-4">
    <article class="tarjetaPes tarjetaCurso h-100 d-flex flex-column" style="--colorArea:<?= e($curso["color"]) ?>">
      <div class="p-4 pb-2 flex-grow-1">
        <span class="etiquetaNivel nivelOtro" style="background:<?= e($curso["color"]) ?>"><?= e($curso["area"]) ?></span>
        <h3 class="h5 mt-3"><?= e($curso["nombre"]) ?></h3>
        <p class="nombreProveedor"><?= e($curso["proveedor"]) ?> · <?= e($curso["nivel"]) ?></p>
        <p class="textoTemas"><?= e($curso["descripcion"]) ?></p>
      </div>
      <div class="pieTarjeta">
        <div class="bloquePrecio">
          <span class="precioActual <?= $curso["precio"] == 0 ? "precioGratis" : "" ?>"><?= formatoPrecio($curso["precio"]) ?></span>
          <?php if ($curso["precioAnterior"]): ?><span class="precioAnterior">$<?= number_format($curso["precioAnterior"], 2) ?></span><?php endif; ?>
        </div>
        <div class="botonesTarjeta">
          <form action="carrito.php" method="post" class="formCarrito">
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="codigo" value="<?= e($curso["codigo"]) ?>">
            <button type="submit" class="btn btnCarrito"><i class="bi bi-cart-plus"></i> Añadir</button>
          </form>
          <a class="btn btnIrCurso" href="../html/<?= e($curso["pagina"]) ?>#<?= e($curso["codigo"]) ?>"><i class="bi bi-eye"></i> Ver</a>
        </div>
      </div>
    </article>
  </div>
  <?php endforeach; ?>
</div>

<?php require "pie.php"; ?>
