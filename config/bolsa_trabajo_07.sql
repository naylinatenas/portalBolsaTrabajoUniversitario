-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS bolsa_trabajo_07
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE bolsa_trabajo_07;

-- ==========================================
-- Tabla: usuario
-- ==========================================
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    rol ENUM('admin','empresa','estudiante') NOT NULL,
    estado TINYINT DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- Tabla: empresa
-- ==========================================
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

-- ==========================================
-- Tabla: estudiante
-- ==========================================
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

-- ==========================================
-- Tabla: oferta
-- ==========================================
CREATE TABLE oferta (
    id_oferta INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    tipo ENUM('prácticas','part-time','full-time'),
    salario_referencial DECIMAL(10,2),
    modalidad ENUM('presencial','remoto','mixto'),
    fecha_publicacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre DATE,
    estado_oferta ENUM('activa','pausada','cerrada') DEFAULT 'activa',
    FOREIGN KEY (empresa_id) REFERENCES empresa(id_empresa)
);

-- ==========================================
-- Tabla: postulacion
-- ==========================================
CREATE TABLE postulacion (
    id_postulacion INT AUTO_INCREMENT PRIMARY KEY,
    oferta_id INT NOT NULL,
    estudiante_id INT NOT NULL,
    fecha_postulacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado_postulacion ENUM('enviada','revisada','aceptada','rechazada') DEFAULT 'enviada',
    comentario_empresa TEXT,
    FOREIGN KEY (oferta_id) REFERENCES oferta(id_oferta),
    FOREIGN KEY (estudiante_id) REFERENCES estudiante(id_estudiante),
    UNIQUE (oferta_id, estudiante_id) -- evita postulaciones duplicadas
);

-- ==========================================
-- Tabla opcional: catalogo_carreras
-- ==========================================
CREATE TABLE catalogo_carreras (
    id_carrera INT AUTO_INCREMENT PRIMARY KEY,
    nombre_carrera VARCHAR(100) NOT NULL
);

-- ==========================================
-- DATOS DE PRUEBA
-- ==========================================

-- Admin
INSERT INTO usuario (nombre_completo, correo, clave, rol, estado)
VALUES ('Administrador Bolsa', 'admin@uni.edu', PASSWORD('admin123'), 'admin', 1);

-- Empresa (pendiente)
INSERT INTO usuario (nombre_completo, correo, clave, rol, estado)
VALUES ('Empresa Ejemplo', 'empresa@correo.com', PASSWORD('empresa123'), 'empresa', 1);

INSERT INTO empresa (razon_social, ruc, direccion, telefono, correo_contacto, usuario_id, estado)
VALUES ('TechCorp S.A.C.', '20112233445', 'Av. Principal 123', '999888777', 'rrhh@techcorp.com', 2, 'aprobada');

-- Estudiante
INSERT INTO usuario (nombre_completo, correo, clave, rol, estado)
VALUES ('Juan Pérez', 'juan@uni.edu', PASSWORD('juan123'), 'estudiante', 1);

INSERT INTO estudiante (usuario_id, codigo_estudiante, carrera, ciclo, resumen_perfil)
VALUES (3, '20231234', 'Ingeniería de Sistemas', '8', 'Estudiante con interés en desarrollo web.');

-- Oferta ejemplo
INSERT INTO oferta (empresa_id, titulo, descripcion, tipo, salario_referencial, modalidad, fecha_cierre)
VALUES (1, 'Practicante de Desarrollo Web', 'Apoyo en proyectos PHP y MySQL.', 'prácticas', 1200.00, 'remoto', '2025-12-31');
