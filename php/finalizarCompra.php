<?php
session_start();
require "conexion.php";
require "funciones.php";

$carrito = calcularCarrito($conexion);

// Si el carrito está vacío no hay nada que comprar
if (count($carrito["productos"]) == 0 && !isset($_SESSION["ultimoPedido"])) {
    header("Location: carrito.php");
    exit;
}

$errores = [];
$nombre = "";
$correo = "";
$idPedido = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && count($carrito["productos"]) > 0) {
    $nombre = trim($_POST["nombreCliente"] ?? "");
    $correo = trim($_POST["correoCliente"] ?? "");

    if (!validarCsrf()) {
        $errores[] = "La sesión expiró, intenta de nuevo.";
    }
    if ($nombre == "" || strlen($nombre) > 100) {
        $errores[] = "Escribe tu nombre completo.";
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 120) {
        $errores[] = "El correo electrónico no es válido.";
    }

    if (count($errores) == 0) {
        // Transacción: o se guarda todo el pedido o no se guarda nada
        $conexion->begin_transaction();
        try {
            // 1. Crear el pedido con el procedimiento almacenado
            $total = $carrito["total"];
            $stmt = $conexion->prepare("CALL sp_crearPedido(?, ?, ?)");
            $stmt->bind_param("ssd", $nombre, $correo, $total);
            $stmt->execute();
            $fila = $stmt->get_result()->fetch_assoc();
            $idPedido = $fila["idPedido"];
            $stmt->close();

            // 2. Guardar cada producto del carrito
            $stmt = $conexion->prepare("INSERT INTO detallePedido (idPedido, tipo, codigo, nombreProducto, idArea, precio) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($carrito["productos"] as $producto) {
                $stmt->bind_param("isssid", $idPedido, $producto["tipo"], $producto["codigo"], $producto["nombre"], $producto["idArea"], $producto["precioFinal"]);
                $stmt->execute();
            }
            $stmt->close();

            $conexion->commit();
            $_SESSION["carrito"] = [];
            $_SESSION["ultimoPedido"] = ["id" => $idPedido, "nombre" => $nombre, "total" => $total];
            header("Location: finalizarCompra.php");
            exit;
        } catch (mysqli_sql_exception $error) {
            $conexion->rollback();
            $errores[] = "No se pudo registrar el pedido. Intenta más tarde.";
        }
    }
}

$tituloPagina = "Finalizar compra";
$subtituloPagina = "Ingresa tus datos para registrar el pedido.";
$colorTema = "#c2571a";
$colorTemaOscuro = "#7f3910";
$paginaActual = "carrito";
require "encabezado.php";
?>

<?php if (count($carrito["productos"]) == 0 && isset($_SESSION["ultimoPedido"])): ?>
  <?php $pedido = $_SESSION["ultimoPedido"]; unset($_SESSION["ultimoPedido"]); ?>
  <div class="d-flex justify-content-center">
    <div class="formularioSugerencias text-center">
      <i class="bi bi-bag-check-fill text-success display-4"></i>
      <h2 class="h4 mt-3">¡Compra registrada!</h2>
      <p>Gracias, <?= e($pedido["nombre"]) ?>. Tu número de pedido es <strong>#<?= (int) $pedido["id"] ?></strong> por un total de <strong><?= formatoPrecio($pedido["total"]) ?></strong>.</p>
      <a class="btn btnOficial" href="../html/index.html">Volver al inicio</a>
    </div>
  </div>
<?php else: ?>
  <div class="row g-4">
    <div class="col-lg-7">
      <form action="finalizarCompra.php" method="post" class="formularioSugerencias mw-100">
        <h2 class="h5 mb-3">Datos del comprador</h2>
        <?php if (count($errores) > 0): ?>
          <div class="alert alert-danger"><?php foreach ($errores as $error): ?><div><?= e($error) ?></div><?php endforeach; ?></div>
        <?php endif; ?>
        <input type="hidden" name="token" value="<?= tokenCsrf() ?>">
        <div class="mb-3">
          <label for="nombreCliente" class="form-label">Nombre completo</label>
          <input type="text" class="form-control" id="nombreCliente" name="nombreCliente" maxlength="100" value="<?= e($nombre) ?>" required>
        </div>
        <div class="mb-3">
          <label for="correoCliente" class="form-label">Correo electrónico</label>
          <input type="email" class="form-control" id="correoCliente" name="correoCliente" maxlength="120" value="<?= e($correo) ?>" required>
        </div>
        <p class="small textoSuave">Proyecto académico: no se realiza ningún cobro real.</p>
        <button type="submit" class="btn btnOficial btn-lg"><i class="bi bi-bag-check"></i> Confirmar pedido</button>
      </form>
    </div>
    <div class="col-lg-5">
      <div class="tarjetaPes p-4">
        <h2 class="h5">Resumen</h2>
        <ul class="list-unstyled mb-0">
          <?php foreach ($carrito["productos"] as $producto): ?>
            <li class="d-flex justify-content-between border-bottom py-2"><span><?= e($producto["nombre"]) ?></span><strong><?= formatoPrecio($producto["precioFinal"]) ?></strong></li>
          <?php endforeach; ?>
          <li class="d-flex justify-content-between pt-2 fs-5"><span>Total</span><strong><?= formatoPrecio($carrito["total"]) ?></strong></li>
        </ul>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php require "pie.php"; ?>
