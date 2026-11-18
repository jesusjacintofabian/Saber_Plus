**Saber Plus**

Sitio web académico que reúne **cursos y certificaciones de tecnología**, organizados por área y nivel, para que quien empieza pueda explorar qué existe y qué se estudia en cada campo.

**Integrantes**

- Mendoza Luis, 20-23-7674
- Jacinto Jesús, 20-58-9524
- Ivaneth Hacock, 3-760-2486

**Áreas tecnológicas**

Ciberseguridad, Programación, Desarrollo Web, Redes, Bases de Datos, Electrónica, Red Team / Blue Team e Informática Forense.

Cada área tiene su página interna: cursosCiberseguridad.html, cursosProgramacion.html, cursosDesarrolloWeb.html, cursosRedes.html, cursosBasesDeDatos.html, cursosElectronica.html, cursosRedBlueTeam.html y cursosForense.html.

**Características**

- Encabezado y pie de página horizontales en todas las páginas.
- Color distinto por página y por área.
- Logotipo propio (images/logoSaberPlus.svg), también usado como icono de la pestaña.
- Precios y ofertas en cada curso.
- Certificaciones con descuento del 20 % si se compró el curso relacionado.
- Botón **Añadir al carrito** funcional (Proyecto 2): guarda los productos en la sesión y el pedido en la base de datos.
- Botón **Ir al curso**: en la página de Cursos lleva a la ficha interna del curso; en las páginas internas abre la página oficial del curso.
- Diseño adaptable a celular, tableta y computadora.

**Cómo ejecutarlo (Proyecto 2, con PHP y MySQL)**

1. Instala y abre **XAMPP**; enciende **Apache** y **MySQL**.
2. Copia la carpeta del proyecto dentro de `C:\xampp\htdocs\` con el nombre `SaberPlus` (sin espacio).
3. Entra a `http://localhost/phpmyadmin`, pestaña **Importar**, y selecciona `bd/saberplus.sql`. Esto crea la base `saberplus` con sus tablas, datos y procedimientos.
4. Abre `http://localhost/SaberPlus/` en el navegador.
5. Panel de administrador: enlace **Administrador** en el pie de página (`php/login.php`).
   - Usuario: `admin`
   - Contraseña: `Admin123*`
6. Si tu MySQL tiene contraseña, cámbiala en `php/conexion.php`.
7. Se necesita conexión a internet para cargar Bootstrap y Bootstrap Icons.

**Tecnologías**

- HTML5 y CSS3
- PHP 8 (mysqli con sentencias preparadas)
- MySQL / MariaDB con procedimientos almacenados
- [Bootstrap 5.3 (CDN)](https://getbootstrap.com/ "https://getbootstrap.com/")
- [Bootstrap Icons 1.11 (CDN)](https://icons.getbootstrap.com/ "https://icons.getbootstrap.com/")

**Convención de nombres**

Se usa **camelCase** en clases, identificadores y nombres de archivos de imágenes y páginas, por ejemplo: tarjetaCurso, precioActual, iconoDesarrolloWeb.svg, cursosBasesDeDatos.html.

**Notas**

- Los **precios y descuentos son valores de ejemplo** del proyecto; el costo real lo define cada proveedor.
- La sección «Lo que aprenderás» de cada curso es un resumen basado en los temas del curso y debe contrastarse con la página oficial del proveedor.
- El 20 % de descuento en certificaciones se aplica cuando el curso relacionado está en el mismo carrito.
- La compra es solo académica: no se cobra nada, solo se registra el pedido.

**Proyecto 2: base de datos y PHP**

Archivos nuevos:

- `bd/saberplus.sql`: script de la base de datos.
- `php/conexion.php`: conexión a MySQL.
- `php/funciones.php`: funciones comunes (escapar HTML, carrito, token CSRF, verificar admin).
- `php/encabezado.php` y `php/pie.php`: encabezado y pie de las páginas PHP.
- `php/guardarContacto.php`: guarda el formulario de contacto.
- `php/carrito.php` y `php/finalizarCompra.php`: carrito de compras y registro del pedido.
- `php/buscarCursos.php`: consulta de cursos por nombre, proveedor y área.
- `php/login.php`, `php/cerrarSesion.php`: inicio y cierre de sesión del administrador.
- `php/panelAdmin.php`: resumen, reporte de ventas, pedidos y sugerencias con filtros.
- `php/agregarCurso.php`: el administrador ingresa cursos nuevos.

Tablas (7): `areas`, `cursos`, `certificaciones`, `usuarios`, `contactos`, `pedidos`, `detallePedido`.

Procedimientos almacenados:

| Procedimiento | Uso |
| --- | --- |
| `sp_guardarContacto` | Inserta el mensaje del formulario de contacto |
| `sp_buscarCursos` | Búsqueda de cursos por texto y área |
| `sp_crearPedido` | Crea el pedido y devuelve su número |
| `sp_reporteVentas` | Reporte de ventas por área en el panel |

Seguridad aplicada:

- Sentencias preparadas (`prepare` + `bind_param`) en todas las consultas con datos del usuario, para evitar inyección SQL.
- Validación de los datos en el servidor (no solo con `required` en HTML).
- `htmlspecialchars` al mostrar datos, para evitar XSS.
- Contraseñas guardadas con `password_hash` y comprobadas con `password_verify`.
- `session_regenerate_id` al iniciar sesión y páginas de administrador protegidas.
- Token CSRF en los formularios de login, compra y agregar curso.
- El precio se lee siempre de la base de datos, no del formulario.

**Institución**

Universidad Tecnológica de Panamá (UTP) – Facultad de Ingeniería de Sistemas Computacionales (FISC).
