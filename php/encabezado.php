<?php
// Encabezado común de las páginas PHP.
// Antes de incluirlo se definen: $tituloPagina, $colorTema, $colorTemaOscuro y $paginaActual
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($tituloPagina) ?> | Saber Plus</title>
  <link rel="icon" type="image/svg+xml" href="../images/logoSaberPlus.svg">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/estilo.css">
</head>
<body style="--colorTema:<?= $colorTema ?>;--colorTemaOscuro:<?= $colorTemaOscuro ?>">
<header class="encabezadoSitio sticky-top">
  <nav class="navbar navbar-expand-md container-xl">
    <a class="marcaSitio navbar-brand" href="../html/index.html"><img class="logoMarca" src="../images/logoSaberPlus.svg" alt="Logotipo de Saber Plus"><span class="nombreSitio">Saber<span class="plusMarca">Plus</span></span></a>
    <button class="navbar-toggler botonMenu" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-label="Abrir menú"><i class="bi bi-list"></i></button>
    <div class="collapse navbar-collapse" id="menuPrincipal">
      <ul class="navbar-nav ms-auto align-items-md-center gap-md-1">
        <li class="nav-item"><a class="enlaceMenu" href="../html/index.html"><i class="bi bi-house-door"></i> Inicio</a></li>
        <li class="nav-item"><a class="enlaceMenu" href="../html/cursos.html"><i class="bi bi-journal-code"></i> Cursos</a></li>
        <li class="nav-item"><a class="enlaceMenu" href="../html/certificaciones.html"><i class="bi bi-patch-check"></i> Certificaciones</a></li>
        <li class="nav-item"><a class="enlaceMenu" href="../html/contacto.html"><i class="bi bi-envelope"></i> Contacto</a></li>
        <li class="nav-item"><a class="enlaceMenu <?= $paginaActual == "buscar" ? "paginaActual" : "" ?>" href="buscarCursos.php"><i class="bi bi-search"></i> Buscar</a></li>
        <li class="nav-item"><a class="enlaceMenu <?= $paginaActual == "carrito" ? "paginaActual" : "" ?>" href="carrito.php"><i class="bi bi-cart3"></i> Carrito <span class="badge text-bg-warning"><?= cantidadCarrito() ?></span></a></li>
        <?php if (isset($_SESSION["idUsuario"])): ?>
        <li class="nav-item"><a class="enlaceMenu <?= $paginaActual == "admin" ? "paginaActual" : "" ?>" href="panelAdmin.php"><i class="bi bi-speedometer2"></i> Panel</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
</header>
<section class="heroPagina"><div class="container-xl heroContenido">
  <div class="d-flex align-items-center gap-3"><div><h1><?= e($tituloPagina) ?></h1><?php if (!empty($subtituloPagina)): ?><p><?= e($subtituloPagina) ?></p><?php endif; ?></div></div>
</div></section>
<main class="container-xl contenidoPrincipal">
