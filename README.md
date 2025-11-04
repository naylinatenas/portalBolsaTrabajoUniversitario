# 🧑‍💼 Portal de Bolsa de Trabajo Universitaria

Sistema web para la gestión de ofertas laborales y prácticas profesionales entre **empresas aliadas**, **estudiantes** y el **área de bolsa de trabajo** de una universidad.  
Desarrollado en **PHP (MVC)** con **MySQL**, **HTML**, **CSS** y **Bootstrap**.

---

## 🚀 Descripción General
El portal permite:
- A **empresas** publicar y administrar ofertas laborales o de prácticas.
- A **estudiantes** registrarse, completar su perfil y postular a ofertas.
- Al **administrador** (bolsa de trabajo) aprobar empresas, gestionar usuarios y consultar estadísticas.

Incluye manejo de **sesiones**, **roles de usuario** y **cookies** para recordar correo y tema (claro/oscuro).

---

## 👥 Roles del Sistema
| Rol | Funcionalidades principales |
|------|-----------------------------|
| **Administrador** | Aprueba empresas, crea usuarios internos, ve reportes y estadísticas. |
| **Empresa** | Publica, edita o cierra ofertas; revisa postulaciones recibidas. |
| **Estudiante** | Completa perfil, ve ofertas, postula y revisa su historial. |

---

## 🛠️ Requisitos Técnicos
- **Frontend:** HTML, CSS, Bootstrap.  
- **Backend:** PHP (patrón MVC).  
- **Base de datos:** MySQL (mínimo 5 tablas).  
- **Sesiones:** Control de acceso y login por rol.  
- **Cookies:**  
  - `correo_recordado` (login).  
  - `tema_portal` (light/dark).  

---

## 🧩 Estructura del Proyecto (MVC)
- /config          → conexión a la base de datos
- /models          → clases de acceso a datos (OfertaModel, EmpresaModel...)
- /controllers     → lógica de negocio (AuthController, OfertaController...)
- /views           → vistas PHP con Bootstrap
- /views/layout    → header.php, footer.php (tema y logout)

---

## 🗄️ Modelo de Base de Datos (resumen)
**Tablas principales:**
1. `usuario` (admin, empresa, estudiante)
2. `empresa`
3. `estudiante`
4. `oferta`
5. `postulacion`
6. `catalogo_carreras` *(opcional)*

Incluye relaciones **1:N** (empresa→ofertas, oferta→postulaciones) y validaciones de rol.

---

## 📋 Casos de Uso Clave

### 🔐 Autenticación
- Login con correo y contraseña.
- Cookie “Recordar correo”.
- Redirección por rol.
- Logout destruye la sesión.

### 👨‍💼 Administrador
- CRUD de empresas.
- Aprobación/rechazo de registros.
- Reportes: ofertas activas, postulaciones recientes, ranking de empresas.

### 🏢 Empresa
- CRUD de ofertas laborales.
- Ver y gestionar postulaciones recibidas.
- Cambiar estado y dejar comentarios.

### 🎓 Estudiante
- Completar perfil y subir CV.
- Buscar ofertas por tipo/modo.
- Postular y consultar historial.

---

## 🖥️ Pantallas Principales
- `login.php`  
- `dashboard_admin.php`, `dashboard_empresa.php`, `dashboard_estudiante.php`  
- `crud_empresa/*.php`  
- `crud_oferta/*.php`  
- `perfil_estudiante.php`, `historial_postulaciones.php`  

---

## ⚙️ Reglas de Negocio
- Empresas nuevas → estado *pendiente* (requieren aprobación).  
- Un estudiante no puede postular dos veces a la misma oferta.  
- Ofertas cerradas no se muestran públicamente.  
- Toda vista interna verifica `$_SESSION["id_usuario"]`.  
- Control de acceso por rol y redirección con mensaje si no tiene permisos.

---

## 🍪 Cookies
| Nombre | Función |
|---------|----------|
| `correo_recordado` | Guarda el correo del login (7 días). |
| `tema_portal` | Guarda la preferencia de tema claro/oscuro. |

---
