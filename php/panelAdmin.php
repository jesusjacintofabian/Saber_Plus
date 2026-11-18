<?php
session_start();
require "conexion.php";
require "funciones.php";
verificarAdmin();

// ---------- Resumen general ----------
$resumen = $conexion->query("SELECT
    (SELECT COUNT(*) FROM pedidos) AS pedidos,
    (SELECT IFNULL(SUM(total), 0) FROM pedidos) AS ventas,
    (SELECT COUNT(*) FROM contactos) AS sugerencias,
    (SELECT COUNT(*) FROM cursos) AS cursos")->fetch_assoc();

// ---------- Reporte de ventas por área (procedimiento almacenado) ----------
$resultado = $conexion->query("CALL sp_reporteVentas()");
$reporte = $resultado->fetch_all(MYSQLI_ASSOC);
$resultado->free();
$conexion->next_result(); // necesario después de un CALL

// ---------- Pedidos (filtro por correo) ----------
$buscarCorreo = trim($_GET["correo"] ?? "");
$filtroCorreo = "%" . $buscarCorreo . "%";
$stmt = $conexion->prepare("SELECT p.idPedido, p.nombreCliente, p.correoCliente, p.total, p.fecha, COUNT(d.idDetalle) AS productos
                            FROM pedidos p
                            LEFT JOIN detallePedido d ON d.idPedido = p.idPedido
                            WHERE p.correoCliente LIKE ?
                            GROUP BY p.idPedido
                            ORDER BY p.fecha DESC");
$stmt->bind_param("s", $filtroCorreo);
$stmt->execute();
$pedidos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------- Detalle de un pedido ----------
$verPedido = (int) ($_GET["pedido"] ?? 0);
$detalle = [];
if ($verPedido > 0) {
    $stmt = $conexion->prepare("SELECT d.tipo, d.nombreProducto, d.precio, a.nombre AS area
                                FROM detallePedido d INNER JOIN areas a ON a.idArea = d.idArea
                                WHERE d.idPedido = ?");
    $stmt->bind_param("i", $verPedido);
    $stmt->execute();
    $detalle = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// ---------- Sugerencias (filtro por área) ----------
$areas = $conexion->query("SELECT idArea, nombre FROM areas ORDER BY idArea")->fetch_all(MYSQLI_ASSOC);
$areaSugerencia = (int) ($_GET["areaSugerencia"] ?? 0);
$stmt = $conexion->prepare("SELECT c.nombre, c.correo, c.tipo, c.nombreSugerencia, c.motivo, c.comentarios, c.fecha, a.nombre AS area
                            FROM contactos c INNER JOIN areas a ON a.idArea = c.idArea
                            WHERE (? = 0 OR c.idArea = ?)
                            ORDER BY c.fecha DESC");
$stmt->bind_param("ii", $areaSugerencia, $areaSugerencia);
$stmt->execute();
$sugerencias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$tituloPagina = "Panel de administración";
$subtituloPagina = "Bienvenido, " . $_SESSION["nombreAdmin"] . ".";
$colorTema = "#3b4a63";
$colorTemaOscuro = "#232c3b";
$paginaActual = "admin";
require "encabezado.php";
?>

<div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
  <a class="btn btnOficial" href="agregarCurso.php"><i class="bi bi-plus-circle"></i> Agregar curso</a>
  <a class="btn btn-outline-secondary" href="cerrarSesion.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
</div>

<!-- Resumen -->
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3"><div class="tarjetaPes tarjetaResumen p-3"><small>Pedidos</small><strong><?= $resumen["pedidos"] ?></strong></div></div>
  <div class="col-6 col-lg-3"><div class="tarjetaPes tarjetaResumen p-3"><small>Total vendido</small><strong>$<?= number_format($resumen["ventas"], 2) ?></strong></div></div>
  <div class="col-6 col-lg-3"><div class="tarjetaPes tarjetaResumen p-3"><small>Sugerencias</small><strong><?= $resumen["sugerencias"] ?></strong></div></div>
  <div class="col-6 col-lg-3"><div class="tarjetaPes tarjetaResumen p-3"><small>Cursos en la BD</small><strong><?= $resumen["cursos"] ?></strong></div></div>
</div>

<!-- Reporte de ventas -->
<section class="tarjetaPes p-3 p-md-4 mb-4">
  <h2 class="h5">Reporte de ventas por área</h2>
  <div class="table-responsive">
    <table class="table table-sm tablaDatos">
      <thead><tr><th>Área</th><th class="text-end">Productos vendidos</th><th class="text-end">Total vendido</th></tr></thead>
      <tbody>
        <?php foreach ($reporte as $fila): ?>
        <tr><td><?= e($fila["area"]) ?></td><td class="text-end"><?= $fila["productosVendidos"] ?></td><td class="text-end">$<?= number_format($fila["totalVendido"], 2) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Pedidos -->
<section class="tarjetaPes p-3 p-md-4 mb-4" id="pedidos">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
    <h2 class="h5 mb-0">Pedidos</h2>
    <form action="panelAdmin.php#pedidos" method="get" class="d-flex gap-2">
      <input type="text" class="form-control form-control-sm" name="correo" maxlength="120" value="<?= e($buscarCorreo) ?>" placeholder="Buscar por correo">
      <button type="submit" class="btn btn-sm btnOficial"><i class="bi bi-search"></i></button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-sm align-middle tablaDatos">
      <thead><tr><th>#</th><th>Cliente</th><th>Correo</th><th>Fecha</th><th class="text-end">Productos</th><th class="text-end">Total</th><th></th></tr></thead>
      <tbody>
        <?php if (count($pedidos) == 0): ?>
          <tr><td colspan="7" class="textoSuave">No hay pedidos.</td></tr>
        <?php endif; ?>
        <?php foreach ($pedidos as $pedido): ?>
        <tr>
          <td><?= $pedido["idPedido"] ?></td>
          <td><?= e($pedido["nombreCliente"]) ?></td>
          <td><?= e($pedido["correoCliente"]) ?></td>
          <td><?= e($pedido["fecha"]) ?></td>
          <td class="text-end"><?= $pedido["productos"] ?></td>
          <td class="text-end">$<?= number_format($pedido["total"], 2) ?></td>
          <td class="text-end"><a class="btn btn-sm btnIrCurso" href="panelAdmin.php?pedido=<?= $pedido["idPedido"] ?>&correo=<?= urlencode($buscarCorreo) ?>#pedidos">Ver</a></td>
        </tr>
        <?php if ($verPedido == $pedido["idPedido"]): ?>
        <tr><td colspan="7" class="bg-light">
          <strong>Detalle del pedido #<?= $verPedido ?>:</strong>
          <ul class="mb-0">
            <?php foreach ($detalle as $item): ?>
              <li><?= e($item["nombreProducto"]) ?> (<?= $item["tipo"] == "curso" ? "Curso" : "Certificación" ?>, <?= e($item["area"]) ?>) – <?= formatoPrecio($item["precio"]) ?></li>
            <?php endforeach; ?>
          </ul>
        </td></tr>
        <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Sugerencias -->
<section class="tarjetaPes p-3 p-md-4" id="sugerencias">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
    <h2 class="h5 mb-0">Sugerencias recibidas</h2>
    <form action="panelAdmin.php#sugerencias" method="get" class="d-flex gap-2">
      <select class="form-select form-select-sm" name="areaSugerencia">
        <option value="0">Todas las áreas</option>
        <?php foreach ($areas as $area): ?>
          <option value="<?= $area["idArea"] ?>" <?= $area["idArea"] == $areaSugerencia ? "selected" : "" ?>><?= e($area["nombre"]) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-sm btnOficial">Filtrar</button>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-sm tablaDatos">
      <thead><tr><th>Fecha</th><th>Nombre</th><th>Correo</th><th>Área</th><th>Tipo</th><th>Sugerencia</th><th>Motivo</th><th>Comentarios</th></tr></thead>
      <tbody>
        <?php if (count($sugerencias) == 0): ?>
          <tr><td colspan="8" class="textoSuave">No hay sugerencias.</td></tr>
        <?php endif; ?>
        <?php foreach ($sugerencias as $s): ?>
        <tr>
          <td><?= e($s["fecha"]) ?></td>
          <td><?= e($s["nombre"]) ?></td>
          <td><?= e($s["correo"]) ?></td>
          <td><?= e($s["area"]) ?></td>
          <td><?= $s["tipo"] == "curso" ? "Curso" : "Certificación" ?></td>
          <td><?= e($s["nombreSugerencia"]) ?></td>
          <td><?= e($s["motivo"]) ?></td>
          <td><?= e($s["comentarios"]) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php require "pie.php"; ?>
