<?php
session_start();
require "conexion.php";
require "funciones.php";

// Si ya inició sesión, va directo al panel
if (isset($_SESSION["idUsuario"])) {
    header("Location: panelAdmin.php");
    exit;
}

$error = "";
$usuario = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $clave = $_POST["clave"] ?? "";

    if (!validarCsrf()) {
        $error = "La sesión expiró, intenta de nuevo.";
    } elseif ($usuario == "" || $clave == "") {
        $error = "Escribe el usuario y la contraseña.";
    } else {
        // Sentencia preparada: el usuario nunca se pega directo en el SQL
        $stmt = $conexion->prepare("SELECT idUsuario, clave, nombreCompleto FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $datos = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // password_verify compara la contraseña con el hash guardado
        if ($datos && password_verify($clave, $datos["clave"])) {
            session_regenerate_id(true); // evita fijación de sesión
            $_SESSION["idUsuario"] = $datos["idUsuario"];
            $_SESSION["nombreAdmin"] = $datos["nombreCompleto"];
            header("Location: panelAdmin.php");
            exit;
        }
        // Mismo mensaje para usuario o clave incorrectos
        $error = "Usuario o contraseña incorrectos.";
    }
}

$tituloPagina = "Acceso de administrador";
$subtituloPagina = "Inicia sesión para ver reportes, pedidos y sugerencias.";
$colorTema = "#3b4a63";
$colorTemaOscuro = "#232c3b";
$paginaActual = "admin";
require "encabezado.php";
?>
<div class="d-flex justify-content-center">
  <form action="login.php" method="post" class="formularioSugerencias formularioLogin">
    <div class="text-center mb-3"><i class="bi bi-person-lock display-5" style="color:var(--colorTema)"></i></div>
    <?php if ($error != ""): ?>
      <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>
    <input type="hidden" name="token" value="<?= tokenCsrf() ?>">
    <div class="mb-3">
      <label for="usuario" class="form-label">Usuario</label>
      <input type="text" class="form-control" id="usuario" name="usuario" maxlength="40" value="<?= e($usuario) ?>" required autofocus>
    </div>
    <div class="mb-3">
      <label for="clave" class="form-label">Contraseña</label>
      <input type="password" class="form-control" id="clave" name="clave" required>
    </div>
    <button type="submit" class="btn btnOficial w-100">Iniciar sesión</button>
  </form>
</div>
<?php require "pie.php"; ?>
