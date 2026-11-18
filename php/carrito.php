<?php
session_start();
require "conexion.php";
require "funciones.php";

if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

// Acciones del carrito: agregar, quitar o vaciar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST["accion"] ?? "";
    $codigo = $_POST["codigo"] ?? "";

    // El código solo puede tener letras y números
    if (!preg_match('/^[A-Za-z0-9]{1,80}$/', $codigo)) {
        $codigo = "";
    }

    if ($accion == "agregar" && $codigo != "") {
        // Solo se agrega si el producto existe en la BD y no está repetido
        if (obtenerProducto($conexion, $codigo) && !in_array($codigo, $_SESSION["carrito"])) {
            $_SESSION["carrito"][] = $codigo;
        }
    } elseif ($accion == "quitar") {
        $_SESSION["carrito"] = array_values(array_diff($_SESSION["carrito"], [$codigo]));
    } elseif ($accion == "vaciar") {
        $_SESSION["carrito"] = [];
    }

    // Redirigir para que al recargar no se vuelva a enviar el formulario
    header("Location: carrito.php");
    exit;
}

$carrito = calcularCarrito($conexion);

$tituloPagina = "Carrito de compras";
$subtituloPagina = "Revisa tus cursos y certificaciones antes de finalizar la compra.";
$colorTema = "#c2571a";
$colorTemaOscuro = "#7f3910";
$paginaActual = "carrito";
require "encabezado.php";
?>

<?php if (count($carrito["productos"]) == 0): ?>
  <div class="tarjetaPes p-5 text-center">
    <i class="bi bi-cart-x display-4 textoSuave"></i>
    <h2 class="h4 mt-3">Tu carrito está vacío</h2>
    <p class="textoSuave">Agrega cursos o certificaciones desde sus páginas.</p>
    <a class="btn btnOficial" href="../html/cursos.html">Ver cursos</a>
    <a class="btn btnIrCurso" href="../html/certificaciones.html">Ver certificaciones</a>
  </div>
<?php else: ?>
  <div class="tarjetaPes p-3 p-md-4">
    <div class="table-responsive">
      <table class="table align-middle tablaDatos">
        <thead>
          <tr><th>Producto</th><th>Tipo</th><th class="text-end">Precio</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($carrito["productos"] as $producto): ?>
          <tr>
            <td><strong><?= e($producto["nombre"]) ?></strong><br><small class="textoSuave"><?= e($producto["proveedor"]) ?></small></td>
            <td><?= $producto["tipo"] == "curso" ? "Curso" : "Certificación" ?></td>
            <td class="text-end">
              <?php if ($producto["descuento"]): ?>
                <span class="precioAnterior"><?= formatoPrecio($producto["precio"]) ?></span>
                <strong><?= formatoPrecio($producto["precioFinal"]) ?></strong>
                <br><small class="estadoDescuento conDescuento">-20% por llevar el curso relacionado</small>
              <?php else: ?>
                <strong><?= formatoPrecio($producto["precioFinal"]) ?></strong>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <form action="carrito.php" method="post">
                <input type="hidden" name="accion" value="quitar">
                <input type="hidden" name="codigo" value="<?= e($producto["codigo"]) ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Quitar"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><th colspan="2" class="text-end">Total</th><th class="text-end fs-5"><?= formatoPrecio($carrito["total"]) ?></th><th></th></tr>
        </tfoot>
      </table>
    </div>
    <div class="d-flex flex-wrap justify-content-between gap-2">
      <form action="carrito.php" method="post">
        <input type="hidden" name="accion" value="vaciar">
        <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Vaciar carrito</button>
      </form>
      <div class="d-flex gap-2">
        <a class="btn btnIrCurso" href="../html/cursos.html">Seguir comprando</a>
        <a class="btn btnOficial" href="finalizarCompra.php"><i class="bi bi-bag-check"></i> Finalizar compra</a>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php require "pie.php"; ?>
