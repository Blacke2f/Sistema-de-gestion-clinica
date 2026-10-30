# SISGECLIN — Sistema de Gestión Clínica
**Laravel 13 | PHP 8.2+ | MySQL | v2.5**

Sistema web para la gestión integral de una clínica médica. Desarrollado con Laravel 13 sobre PHP 8.2+, permite administrar pacientes, consultas médicas, inventario de medicamentos, nómina del personal, mensajería interna y exportación de reportes en múltiples formatos.

---

## Acceso rápido

| Entorno | URL |
|---|---|
| Local | `http://localhost/SISGECLIN/public` |
| Red local | `http://<IP-DEL-SERVIDOR>/SISGECLIN/public` |

## Credenciales por defecto

| Usuario | Contraseña | Rol |
|---|---|---|
| `admin` | `admin123` | Administrador |
| `medico` | `admin123` | Médico |
| `recepcion` | `admin123` | Recepcionista |

> ⚠️ Cambiar contraseñas antes de usar en producción.

---

## Instalación

```bash
# 1. Instalar dependencias PHP
composer install

# 2. Configurar entorno
copy .env.example .env
# Editar .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Crear la base de datos en MySQL
# CREATE DATABASE sisgeclin_laravel CHARACTER SET utf8mb4;

# 4. Importar el SQL con datos iniciales
# phpMyAdmin → importar database/sisgeclin_laravel.sql

# 5. Limpiar cachés
php artisan config:clear
php artisan view:clear
```

---

## Stack tecnológico

| Componente | Tecnología |
|---|---|
| Framework | Laravel 13 |
| Lenguaje | PHP 8.2+ |
| Base de datos | MySQL / MariaDB |
| ORM | Eloquent |
| PDF | mPDF 8.3 |
| Excel | PhpSpreadsheet 5.7 |
| Frontend | CSS/JS personalizado + Boxicons |

---

## Módulos del sistema

| Módulo | Descripción |
|---|---|
| Autenticación | Login, logout, registro con activación por admin |
| Dashboard | Estadísticas en tiempo real |
| Pacientes | CRUD completo con búsqueda y exportación |
| Consultas | Gestión de consultas médicas con estados |
| Medicamentos | Catálogo con alertas de stock bajo |
| Movimientos | Entradas y salidas de inventario con firma |
| Solicitudes | Solicitudes de reposición con número de orden |
| Nómina | Personal médico y administrativo |
| Usuarios | Gestión de cuentas con permisos granulares |
| Mensajes | Mensajería interna individual y masiva |
| Notificaciones | Alertas en tiempo real con polling |
| Bitácora | Auditoría completa de acciones |
| Exportaciones | PDF, Excel y CSV en todos los módulos |
| Búsqueda AJAX | Búsqueda global en tiempo real |

---

## Documentación

| Documento | Descripción |
|---|---|
| [`docs/manual_usuario.html`](docs/manual_usuario.html) | Guía completa de uso para usuarios finales |
| [`docs/documentacion_tecnica.html`](docs/documentacion_tecnica.html) | Arquitectura, instalación, API de rutas |
| [`docs/documentacion_codigo.html`](docs/documentacion_codigo.html) | Cómo funciona el código, módulo por módulo |
| [`docs/modelo_er.html`](docs/modelo_er.html) | Diagramas ER y UML interactivos |
| [`database/sisgeclin_laravel.sql`](database/sisgeclin_laravel.sql) | Dump completo de la base de datos |

---

## Acceso en red local

Ejecutar `ABRIR_FIREWALL.bat` como Administrador para habilitar el puerto 80 en el firewall de Windows. El sistema detecta automáticamente el host y funciona desde cualquier IP sin modificar el `.env`.
