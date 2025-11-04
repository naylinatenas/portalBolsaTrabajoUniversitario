
-- ==========================================
-- BASE DE DATOS: Portal de Bolsa de Trabajo
Universitaria
-- Grupo 7 
-- Integrantes:
-- Acosta Plascencia, Naylin Atenas
-- Chuquipoma Medina, Sthefany Darley
-- Mantilla Sanchez, Elsa Lucia
-- ==========================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS bolsa_trabajo_07
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE bolsa_trabajo_07;

-- =========================
-- Tabla: usuario
-- =========================
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,  -- Guardar con password_hash() desde PHP
    rol ENUM('admin', 'empresa', 'estudiante') NOT NULL,
    estado TINYINT DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Tabla: empresa
-- =========================
CREATE TABLE empresa (
    id_empresa INT AUTO_INCREMENT PRIMARY KEY,
    razon_social VARCHAR(100) NOT NULL,
    ruc VARCHAR(20),
    direccion VARCHAR(150),
    telefono VARCHAR(20),
    correo_contacto VARCHAR(100),
    usuario_id INT NOT NULL,
    estado ENUM('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
    FOREIGN KEY (usuario_id) REFERENCES usuario(id_usuario)
);

-- =========================
-- Tabla: estudiante
-- =========================
CREATE TABLE estudiante (
    id_estudiante INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    codigo_estudiante VARCHAR(20),
    carrera VARCHAR(100),
    ciclo VARCHAR(10),
    cv_url VARCHAR(255),
    resumen_perfil TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id_usuario)
);

-- =========================
-- Tabla: oferta
-- =========================
CREATE TABLE oferta (
    id_oferta INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    tipo ENUM('practicas','part-time','full-time'),
    salario_referencial DECIMAL(10,2),
    modalidad ENUM('presencial','remoto','mixto'),
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre DATE,
    estado_oferta ENUM('activa','pausada','cerrada') DEFAULT 'activa',
    FOREIGN KEY (empresa_id) REFERENCES empresa(id_empresa)
);

-- =========================
-- Tabla: postulacion
-- =========================
CREATE TABLE postulacion (
    id_postulacion INT AUTO_INCREMENT PRIMARY KEY,
    oferta_id INT NOT NULL,
    estudiante_id INT NOT NULL,
    fecha_postulacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado_postulacion ENUM('enviada','revisada','aceptada','rechazada') DEFAULT 'enviada',
    comentario_empresa TEXT,
    FOREIGN KEY (oferta_id) REFERENCES oferta(id_oferta),
    FOREIGN KEY (estudiante_id) REFERENCES estudiante(id_estudiante),
    UNIQUE (oferta_id, estudiante_id) -- Evita postular dos veces a la misma oferta
);

-- =========================
-- Tabla opcional: catálogo de carreras
-- =========================
CREATE TABLE catalogo_carreras (
    id_carrera INT AUTO_INCREMENT PRIMARY KEY,
    nombre_carrera VARCHAR(100) NOT NULL
);

USE bolsa_trabajo_07;

-- ======================================
-- INSERTS EN TABLA usuario
-- ======================================
INSERT INTO usuario (nombre_completo, correo, clave, rol, estado)
VALUES 
('Administrador Bolsa UCV', 'admin@ucv.edu.pe', SHA2('admin123',256), 'admin', 1),

-- Empresas
('Recursos Humanos TechCorp', 'contacto@techcorp.com', SHA2('tech123',256), 'empresa', 1),
('RRHH AgroPerú S.A.C.', 'rrhh@agroperu.com', SHA2('agro123',256), 'empresa', 1),

-- Estudiantes UCV
('María Fernanda López', 'maria.lopez@ucv.edu.pe', SHA2('maria123',256), 'estudiante', 1),
('Carlos Alberto Chávez', 'carlos.chavez@ucv.edu.pe', SHA2('carlos123',256), 'estudiante', 1),
('Lucía Ramos Torres', 'lucia.ramos@ucv.edu.pe', SHA2('lucia123',256), 'estudiante', 1);

-- ======================================
-- INSERTS EN TABLA empresa
-- ======================================
INSERT INTO empresa (razon_social, ruc, direccion, telefono, correo_contacto, usuario_id, estado)
VALUES 
('TechCorp S.A.C.', '20567891234', 'Av. Larco 345, Trujillo', '944123456', 'contacto@techcorp.com', 2, 'aprobada'),
('AgroPerú S.A.C.', '20678912345', 'Av. Mansiche 789, Trujillo', '955987654', 'rrhh@agroperu.com', 3, 'aprobada');

-- ======================================
-- INSERTS EN TABLA estudiante
-- ======================================
INSERT INTO estudiante (usuario_id, codigo_estudiante, carrera, ciclo, cv_url, resumen_perfil)
VALUES
(4, '2023124501', 'Ingeniería de Sistemas', '8', 'https://cvucv.com/maria-lopez.pdf', 'Estudiante con interés en desarrollo web y análisis de datos.'),
(5, '2023124502', 'Ingeniería Industrial', '7', 'https://cvucv.com/carlos-chavez.pdf', 'Interés en logística y gestión empresarial.'),
(6, '2023124503', 'Administración de Empresas', '9', 'https://cvucv.com/lucia-ramos.pdf', 'Orientada al marketing digital y recursos humanos.');

-- ======================================
-- INSERTS EN TABLA oferta
-- ======================================
INSERT INTO oferta (empresa_id, titulo, descripcion, tipo, salario_referencial, modalidad, fecha_cierre)
VALUES
(1, 'Practicante de Desarrollo Web', 'Apoyo en desarrollo de sistemas con PHP, Laravel y MySQL.', 'practicas', 1200.00, 'remoto', '2025-12-31'),
(1, 'Asistente de Soporte Técnico', 'Brindar soporte a usuarios, mantenimiento de equipos e instalaciones de software.', 'part-time', 1000.00, 'presencial', '2025-11-30'),
(2, 'Practicante de Logística', 'Apoyo en control de inventarios, guías de remisión y órdenes de compra.', 'practicas', 1100.00, 'mixto', '2025-12-15');

-- ======================================
-- INSERTS EN TABLA postulacion
-- ======================================
INSERT INTO postulacion (oferta_id, estudiante_id, estado_postulacion, comentario_empresa)
VALUES
(1, 1, 'enviada', NULL),
(1, 2, 'revisada', 'Buen perfil, pendiente entrevista.'),
(3, 3, 'aceptada', 'Seleccionada para prácticas en logística.');

-- ======================================
-- INSERTS EN TABLA catalogo_carreras
-- ======================================
INSERT INTO catalogo_carreras (nombre_carrera)
VALUES
('Ingeniería de Sistemas'),
('Ingeniería Industrial'),
('Administración de Empresas'),
('Contabilidad'),
('Derecho'),
('Ingeniería Civil');
