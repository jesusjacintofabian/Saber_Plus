<?php
// Funciones que se usan en varias páginas

// Escapa el texto antes de mostrarlo en HTML (evita XSS)
function e($texto)
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, "UTF-8");
}

// Muestra el precio como $25 o Gratis
function formatoPrecio($precio)
{
    if ($precio == 0) {
        return "Gratis";
    }
    return "$" . number_format($precio, 2);
}

// Cantidad de productos en el carrito (para el menú)
function cantidadCarrito()
{
    return isset($_SESSION["carrito"]) ? count($_SESSION["carrito"]) : 0;
}

// Busca un curso o una certificación por su código.
// El precio siempre se toma de la BD, nunca del formulario.
function obtenerProducto($conexion, $codigo)
{
    $sql = "SELECT 'curso' AS tipo, codigo, nombre, proveedor, precio, idArea, NULL AS codigoRelacionado
            FROM cursos WHERE codigo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($producto) {
        return $producto;
    }

    $sql = "SELECT 'certificacion' AS tipo, ce.codigo, ce.nombre, ce.proveedor, ce.precio, ce.idArea,
                   cu.codigo AS codigoRelacionado
            FROM certificaciones ce
            LEFT JOIN cursos cu ON cu.idCurso = ce.idCursoRelacionado
            WHERE ce.codigo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $producto; // null si no existe
}

// Arma la lista del carrito con precios finales.
// Regla del sitio: 20% de descuento en la certificación si el curso relacionado está en el carrito.
function calcularCarrito($conexion)
{
    $productos = [];
    $total = 0;
    $codigos = isset($_SESSION["carrito"]) ? $_SESSION["carrito"] : [];

    foreach ($codigos as $codigo) {
        $producto = obtenerProducto($conexion, $codigo);
        if (!$producto) {
            continue;
        }
        $producto["descuento"] = false;
        $producto["precioFinal"] = $producto["precio"];

        if ($producto["tipo"] == "certificacion" && in_array($producto["codigoRelacionado"], $codigos)) {
            $producto["descuento"] = true;
            $producto["precioFinal"] = round($producto["precio"] * 0.8, 2);
        }

        $total += $producto["precioFinal"];
        $productos[] = $producto;
    }

    return ["productos" => $productos, "total" => $total];
}

// Token para proteger los formularios del administrador (CSRF)
function tokenCsrf()
{
    if (empty($_SESSION["token"])) {
        $_SESSION["token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["token"];
}

function validarCsrf()
{
    return isset($_POST["token"], $_SESSION["token"]) && hash_equals($_SESSION["token"], $_POST["token"]);
}

// Si no hay sesión de administrador, regresa al login
function verificarAdmin()
{
    if (!isset($_SESSION["idUsuario"])) {
        header("Location: login.php");
        exit;
    }
}
