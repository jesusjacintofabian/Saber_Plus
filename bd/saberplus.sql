-- =========================================================
-- Proyecto 2 - Desarrollo Web
-- Base de datos del sitio Saber Plus
-- Integrantes: Mendoza Luis, Jacinto Jesús, Ivaneth Hacock
-- =========================================================
-- Importar en phpMyAdmin (pestaña Importar) o con:
--   mysql -u root -p < saberplus.sql

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS saberplus;
CREATE DATABASE saberplus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE saberplus;

-- ---------------------------------------------------------
-- TABLAS
-- ---------------------------------------------------------

-- Áreas tecnológicas del sitio
CREATE TABLE areas (
  idArea INT PRIMARY KEY,
  codigo VARCHAR(30) NOT NULL UNIQUE,
  nombre VARCHAR(60) NOT NULL,
  pagina VARCHAR(60) NOT NULL,
  color VARCHAR(10) NOT NULL
);

-- Cursos (el código es el mismo id que tiene el curso en su página HTML)
CREATE TABLE cursos (
  idCurso INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(80) NOT NULL UNIQUE,
  idArea INT NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  proveedor VARCHAR(80) NOT NULL,
  nivel VARCHAR(40) NOT NULL,
  descripcion VARCHAR(255),
  precio DECIMAL(8,2) NOT NULL DEFAULT 0,
  precioAnterior DECIMAL(8,2) NULL,
  urlOficial VARCHAR(255),
  FOREIGN KEY (idArea) REFERENCES areas(idArea)
);

-- Certificaciones (tienen 20% de descuento si se compra el curso relacionado)
CREATE TABLE certificaciones (
  idCertificacion INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(80) NOT NULL UNIQUE,
  idArea INT NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  proveedor VARCHAR(80) NOT NULL,
  nivel VARCHAR(40) NOT NULL,
  precio DECIMAL(8,2) NOT NULL DEFAULT 0,
  idCursoRelacionado INT NULL,
  FOREIGN KEY (idArea) REFERENCES areas(idArea),
  FOREIGN KEY (idCursoRelacionado) REFERENCES cursos(idCurso)
);

-- Usuarios administradores (la clave se guarda con password_hash de PHP)
CREATE TABLE usuarios (
  idUsuario INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(40) NOT NULL UNIQUE,
  clave VARCHAR(255) NOT NULL,
  nombreCompleto VARCHAR(100) NOT NULL,
  fechaCreacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Mensajes del formulario de contacto / sugerencias
CREATE TABLE contactos (
  idContacto INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(120) NOT NULL,
  idArea INT NOT NULL,
  tipo ENUM('curso','certificacion') NOT NULL,
  nombreSugerencia VARCHAR(150) NOT NULL,
  motivo VARCHAR(255) NOT NULL,
  comentarios TEXT,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (idArea) REFERENCES areas(idArea)
);

-- Pedidos del carrito de compras
CREATE TABLE pedidos (
  idPedido INT AUTO_INCREMENT PRIMARY KEY,
  nombreCliente VARCHAR(100) NOT NULL,
  correoCliente VARCHAR(120) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Productos de cada pedido (se guarda el precio con el que se vendió)
CREATE TABLE detallePedido (
  idDetalle INT AUTO_INCREMENT PRIMARY KEY,
  idPedido INT NOT NULL,
  tipo ENUM('curso','certificacion') NOT NULL,
  codigo VARCHAR(80) NOT NULL,
  nombreProducto VARCHAR(120) NOT NULL,
  idArea INT NOT NULL,
  precio DECIMAL(8,2) NOT NULL,
  FOREIGN KEY (idPedido) REFERENCES pedidos(idPedido) ON DELETE CASCADE,
  FOREIGN KEY (idArea) REFERENCES areas(idArea)
);

-- ---------------------------------------------------------
-- DATOS INICIALES
-- ---------------------------------------------------------

INSERT INTO areas (idArea, codigo, nombre, pagina, color) VALUES
(1, 'ciberseguridad', 'Ciberseguridad', 'cursosCiberseguridad.html', '#1f4e8c'),
(2, 'programacion', 'Programación', 'cursosProgramacion.html', '#6a3fa0'),
(3, 'desarrolloWeb', 'Desarrollo Web', 'cursosDesarrolloWeb.html', '#c2571a'),
(4, 'redes', 'Redes', 'cursosRedes.html', '#0f7a8a'),
(5, 'basesDeDatos', 'Bases de Datos', 'cursosBasesDeDatos.html', '#2f6b3f'),
(6, 'electronica', 'Electrónica', 'cursosElectronica.html', '#b8860b'),
(7, 'redBlueTeam', 'Red Team / Blue Team', 'cursosRedBlueTeam.html', '#a12b3d'),
(8, 'forense', 'Informática Forense', 'cursosForense.html', '#4a3d7a');

INSERT INTO cursos (codigo, idArea, nombre, proveedor, nivel, descripcion, precio, precioAnterior, urlOficial) VALUES
('cursoIntroductionToCybersecurity', 1, 'Introduction to Cybersecurity', 'Cisco Networking Academy', 'Principiante', 'Introduce conceptos de amenazas, protección de datos y seguridad en línea.', 0.00, NULL, 'https://www.netacad.com'),
('cursoFoundationsOfCybersecurity', 1, 'Foundations of Cybersecurity', 'Google (en Coursera)', 'Principiante', 'Introduce el campo de la ciberseguridad y sus roles, riesgos y herramientas básicas.', 39.00, 49.00, 'https://www.coursera.org'),
('cursoCertifiedInCybersecurityCursoDePreparaciN', 1, 'Certified in Cybersecurity: curso de preparación', 'ISC2', 'Principiante', 'Orientado a principios de seguridad, controles de acceso y respuesta a incidentes.', 59.00, 79.00, 'https://www.isc2.org'),
('cursoCs50xIntroductionToComputerScience', 2, 'CS50x: Introduction to Computer Science', 'Harvard University (CS50)', 'Principiante', 'Introduce ciencia de la computación: algoritmos, estructuras de datos y resolución de problemas.', 0.00, NULL, 'https://cs50.harvard.edu/x/'),
('cursoPythonEssentials1', 2, 'Python Essentials 1', 'Python Institute', 'Principiante', 'Introduce la sintaxis básica de Python, variables, control de flujo y funciones.', 29.00, 39.00, 'https://pythoninstitute.org'),
('cursoScientificComputingWithPython', 2, 'Scientific Computing with Python', 'freeCodeCamp', 'Principiante', 'Práctica de programación en Python con proyectos guiados.', 0.00, NULL, 'https://www.freecodecamp.org'),
('cursoResponsiveWebDesign', 3, 'Responsive Web Design', 'freeCodeCamp', 'Principiante', 'Introduce HTML, CSS y diseño adaptable a distintas pantallas.', 0.00, NULL, 'https://www.freecodecamp.org'),
('cursoHtmlTutorial', 3, 'HTML Tutorial', 'W3Schools', 'Principiante', 'Explica la estructura de páginas web con HTML y sus elementos.', 19.00, 25.00, 'https://www.w3schools.com/html/'),
('cursoComputerProgrammingHtmlYCss', 3, 'Computer Programming: HTML y CSS', 'Khan Academy', 'Principiante', 'Introduce la creación de páginas web con HTML y CSS.', 0.00, NULL, 'https://www.khanacademy.org'),
('cursoNetworkingBasics', 4, 'Networking Basics', 'Cisco Networking Academy', 'Principiante', 'Introduce dispositivos, protocolos y direccionamiento.', 25.00, 35.00, 'https://www.netacad.com'),
('cursoNetworkingEssentials', 4, 'Networking Essentials', 'Cisco Networking Academy', 'Principiante', 'Aborda conceptos de conectividad, configuración básica y redes domésticas o pequeñas.', 35.00, 45.00, 'https://www.netacad.com'),
('cursoCcnaIntroductionToNetworks', 4, 'CCNA: Introduction to Networks', 'Cisco Networking Academy', 'Intermedio', 'Aborda modelos de red, direccionamiento IP y configuración de switches y routers.', 79.00, 99.00, 'https://www.netacad.com'),
('cursoIntroToSql', 5, 'Intro to SQL', 'Khan Academy', 'Principiante', 'Introduce consultas SQL y manejo básico de datos en tablas.', 0.00, NULL, 'https://www.khanacademy.org'),
('cursoSqlTutorial', 5, 'SQL Tutorial', 'W3Schools', 'Principiante', 'Explica sentencias SQL para consultar, insertar y modificar datos.', 19.00, 29.00, 'https://www.w3schools.com/sql/'),
('cursoMongodbUniversity', 5, 'MongoDB University', 'MongoDB', 'Principiante, Intermedio', 'Aborda bases de datos no relacionales y el modelado de documentos.', 45.00, 59.00, 'https://learn.mongodb.com'),
('cursoCircuitsAndElectronics6002', 6, 'Circuits and Electronics (6.002)', 'MIT OpenCourseWare', 'Intermedio', 'Aborda análisis de circuitos y fundamentos de electrónica.', 0.00, NULL, 'https://ocw.mit.edu'),
('cursoTutorialesDeArduino', 6, 'Tutoriales de Arduino', 'Arduino', 'Principiante', 'Introduce electrónica básica y programación de microcontroladores.', 15.00, 20.00, 'https://docs.arduino.cc'),
('cursoProyectosDeRaspberryPi', 6, 'Proyectos de Raspberry Pi', 'Raspberry Pi Foundation', 'Principiante', 'Proyectos guiados de computación física y programación.', 15.00, 22.00, 'https://projects.raspberrypi.org'),
('cursoRutasDeAprendizajeDeTryhackme', 7, 'Rutas de aprendizaje de TryHackMe', 'TryHackMe', 'Principiante, Intermedio', 'Prácticas guiadas de seguridad ofensiva y defensiva.', 10.00, 14.00, 'https://tryhackme.com'),
('cursoHackTheBoxAcademy', 7, 'Hack The Box Academy', 'Hack The Box', 'Intermedio', 'Módulos teórico-prácticos sobre seguridad ofensiva y defensiva.', 49.00, 69.00, 'https://academy.hackthebox.com'),
('cursoLaboratoriosDeCyberdefenders', 7, 'Laboratorios de CyberDefenders', 'CyberDefenders', 'Intermedio', 'Ejercicios prácticos orientados a análisis y defensa (Blue Team).', 39.00, 55.00, 'https://cyberdefenders.org'),
('cursoDigitalForensicsEssentialsDfe', 8, 'Digital Forensics Essentials (DFE)', 'EC-Council', 'Principiante', 'Fundamentos y proceso de investigación forense; Windows, Linux y malware.', 29.00, 45.00, 'https://eccouncil.org/academia/digital-forensics-essentials-dfe/'),
('cursoIntroductionToDigitalForensics', 8, 'Introduction to Digital Forensics', 'Alison', 'Principiante', 'Principios de informática forense, aspectos legales, informes y análisis de red y memoria.', 0.00, NULL, 'https://alison.com/course/introduction-to-digital-forensics');

-- El curso relacionado se busca por su código para no depender del id
INSERT INTO certificaciones (codigo, idArea, nombre, proveedor, nivel, precio, idCursoRelacionado) VALUES
('certComptiaSecurity', 1, 'CompTIA Security+', 'CompTIA', 'Intermedio', 392.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoFoundationsOfCybersecurity')),
('certCertifiedInCybersecurityCc', 1, 'Certified in Cybersecurity (CC)', 'ISC2', 'Principiante', 199.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoCertifiedInCybersecurityCursoDePreparaciN')),
('certGoogleCybersecurityCertificate', 1, 'Google Cybersecurity Certificate', 'Google', 'Principiante', 49.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoIntroductionToCybersecurity')),
('certPcepCertifiedEntryLevelPythonProgrammer', 2, 'PCEP – Certified Entry-Level Python Programmer', 'Python Institute', 'Principiante', 59.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoPythonEssentials1')),
('certPcapCertifiedAssociateInPythonProgramming', 2, 'PCAP – Certified Associate in Python Programming', 'Python Institute', 'Intermedio', 295.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoScientificComputingWithPython')),
('certPcpp1CertifiedProfessionalInPythonProgramming', 2, 'PCPP1 – Certified Professional in Python Programming', 'Python Institute', 'Avanzado', 195.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoCs50xIntroductionToComputerScience')),
('certResponsiveWebDesignCertification', 3, 'Responsive Web Design Certification', 'freeCodeCamp', 'Principiante', 0.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoResponsiveWebDesign')),
('certCertificacionesDeW3schools', 3, 'Certificaciones de W3Schools', 'W3Schools', 'Principiante', 95.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoHtmlTutorial')),
('certCiscoCertifiedSupportTechnicianCcstNetworking', 4, 'Cisco Certified Support Technician (CCST) Networking', 'Cisco', 'Principiante', 125.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoNetworkingBasics')),
('certCcna', 4, 'CCNA', 'Cisco', 'Intermedio', 300.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoCcnaIntroductionToNetworks')),
('certComptiaNetwork', 4, 'CompTIA Network+', 'CompTIA', 'Intermedio', 369.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoNetworkingEssentials')),
('certAzureDataFundamentalsDp900', 5, 'Azure Data Fundamentals (DP-900)', 'Microsoft', 'Principiante', 99.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoIntroToSql')),
('certOracleDatabaseSqlCertifiedAssociate', 5, 'Oracle Database SQL Certified Associate', 'Oracle', 'Intermedio', 245.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoSqlTutorial')),
('certMongodbAssociateDeveloper', 5, 'MongoDB Associate Developer', 'MongoDB', 'Intermedio', 150.00, (SELECT idCurso FROM cursos WHERE codigo = 'cursoMongodbUniversity'));

-- Usuario administrador de prueba
-- usuario: admin   contraseña: Admin123*
INSERT INTO usuarios (usuario, clave, nombreCompleto) VALUES
('admin', '$2y$10$R4OP4H9SVh8vkDdL43/qWOpXjcrMq5.8LR60b/FF.QnHaymXRpfUC', 'Administrador Saber Plus');

-- ---------------------------------------------------------
-- PROCEDIMIENTOS ALMACENADOS
-- ---------------------------------------------------------
DELIMITER //

-- 1. Guarda un mensaje del formulario de contacto
CREATE PROCEDURE sp_guardarContacto(
  IN p_nombre VARCHAR(100),
  IN p_correo VARCHAR(120),
  IN p_idArea INT,
  IN p_tipo VARCHAR(20),
  IN p_nombreSugerencia VARCHAR(150),
  IN p_motivo VARCHAR(255),
  IN p_comentarios TEXT
)
BEGIN
  INSERT INTO contactos (nombre, correo, idArea, tipo, nombreSugerencia, motivo, comentarios)
  VALUES (p_nombre, p_correo, p_idArea, p_tipo, p_nombreSugerencia, p_motivo, p_comentarios);
END //

-- 2. Busca cursos por texto (nombre o proveedor) y por área (0 = todas)
CREATE PROCEDURE sp_buscarCursos(
  IN p_texto VARCHAR(100),
  IN p_idArea INT
)
BEGIN
  SELECT c.codigo, c.nombre, c.proveedor, c.nivel, c.descripcion, c.precio, c.precioAnterior,
         a.nombre AS area, a.pagina, a.color
  FROM cursos c
  INNER JOIN areas a ON a.idArea = c.idArea
  WHERE (c.nombre LIKE CONCAT('%', p_texto, '%') OR c.proveedor LIKE CONCAT('%', p_texto, '%'))
    AND (p_idArea = 0 OR c.idArea = p_idArea)
  ORDER BY a.idArea, c.nombre;
END //

-- 3. Crea un pedido y devuelve su número
CREATE PROCEDURE sp_crearPedido(
  IN p_nombre VARCHAR(100),
  IN p_correo VARCHAR(120),
  IN p_total DECIMAL(10,2)
)
BEGIN
  INSERT INTO pedidos (nombreCliente, correoCliente, total) VALUES (p_nombre, p_correo, p_total);
  SELECT LAST_INSERT_ID() AS idPedido;
END //

-- 4. Reporte de ventas por área para el administrador
CREATE PROCEDURE sp_reporteVentas()
BEGIN
  SELECT a.nombre AS area,
         COUNT(d.idDetalle) AS productosVendidos,
         IFNULL(SUM(d.precio), 0) AS totalVendido
  FROM areas a
  LEFT JOIN detallePedido d ON d.idArea = a.idArea
  GROUP BY a.idArea, a.nombre
  ORDER BY totalVendido DESC, a.idArea;
END //

DELIMITER ;
