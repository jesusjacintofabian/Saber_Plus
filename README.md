# ParaElSaber

**Facilitador(a):** Dra. Denis Cedeño
**Fecha:** 28/09/2026
**Título de la experiencia:** Desarrollo Web
**Tema:** Desarrollo de un sitio web
**Objetivo:** Diseñar e implementar un sitio web, incluyendo elementos de HTML5, CSS y Bootstrap.

## Integrantes

- Mendoza Luis, 20-23-7674
- Jacinto Jesús, 20-58-9524
- Dionellys Rodríguez, 3-755-1520
- Ivaneth Hacock, 3-760-2486

## Sobre el proyecto

ParaElSaber es un sitio con cursos y certificaciones de tecnología, organizados por área y nivel. Lo hicimos porque quien empieza en tecnología muchas veces no sabe qué áreas existen ni qué se estudia en cada una. El sitio junta esa información para que se pueda explorar y decidir por dónde empezar.

Está pensado para personas que inician en tecnología y para estudiantes universitarios que quieren conocer distintas áreas.

## Páginas

- **Inicio** (`html/index.html`): qué es el sitio y las áreas disponibles.
- **Cursos** (`html/cursos.html`): cursos por área, con nivel, proveedor, costo e idioma.
- **Certificaciones** (`html/certificaciones.html`): igual que cursos, pero con certificaciones.
- **Contacto** (`html/contacto.html`): formulario para sugerir un curso o certificación.

## Herramientas usadas

- **HTML5:** `header`, `nav`, `main`, `section`, `article`, `footer` y un formulario con `form`, `fieldset`, `label`, `input`, `select` y `textarea`.
- **CSS:** archivo propio (`css/estilo.css`) con variables, media queries y estilos para `:hover` y `:focus`.
- **Bootstrap 5.3.3:** cargado por CDN. Usamos el grid, las tarjetas (`card`), botones y los estilos del formulario.

El logo (PES) está hecho con HTML y CSS. El formulario todavía no guarda datos porque aún no está conectado a una base de datos.

## Ver el sitio

Publicado en GitHub Pages: https://jesusjacintofabian.github.io/ParaElSaber/

Para abrirlo en local, clonar el repositorio y abrir `index.html` en el navegador. Se necesita internet para que cargue Bootstrap.

## Estructura

```
ParaElSaber/
├── index.html
├── css/
├── html/
└── images/
```

El `index.html` de la raíz solo redirige a `html/index.html`, para que GitHub Pages abra el sitio directamente.
