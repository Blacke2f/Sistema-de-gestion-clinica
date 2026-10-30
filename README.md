# SISGECLIN v3.0 — Sistema de Gestión Clínica Institucional
**Laravel 13 | PHP 8.3+ | MariaDB/MySQL | Sin facturación**

Sistema web para la gestión integral de una clínica institucional. Administra pacientes, expediente clínico, agenda de citas, consultas médicas, signos vitales, recetas médicas (digitales y manuscritas), inventario de medicamentos, nómina del personal y reportes estadísticos.

---

## Credenciales

| Usuario | Contraseña | Rol |
|---|---|---|
| `admin` | `admin123` | Administrador |
| `medico` | `admin123` | Médico |
| `recepcion` | `admin123` | Recepcionista |
| `enfermera` | `Admin123` | Enfermera |
| `farmacia` | `Admin123` | Farmacéutico |
| `contador` | `Admin123` | Contador |
| `laboratorio` | `Admin123` | Laboratorista |

> ⚠️ Cambiar contraseñas antes de usar en producción.

---

## Instalación rápida (Laragon)

```bash
# 1. Colocar la carpeta en C:\laragon\www\SISGECLIN

# 2. Instalar dependencias
composer install

# 3. Importar la base de datos en phpMyAdmin
# Importar: database/sisgeclin_laravel.sql

# 4. Ejecutar migraciones pendientes
php artisan migrate --force

# 5. Limpiar cachés
php artisan sisgeclin:clear

# 6. (Opcional) Cargar datos de prueba
php artisan db:seed --class=DatosPruebaSeeder
```

---

## Acceso en red local (WiFi/LAN)

Ejecutar **`ABRIR_FIREWALL.bat`** como Administrador. Detecta Apache automáticamente y abre los puertos necesarios.

## Migración de base de datos

Ejecutar **`MIGRAR_BD.bat`** para migrar a SQL Server o PostgreSQL.

---

## Módulos del sistema (v3.0)

| Módulo | Descripción |
|---|---|
| Dashboard | KPIs, gráficas Chart.js, citas del día |
| Pacientes | CRUD + Registro rápido desde consulta |
| Expediente Clínico | Historial unificado del paciente |
| Agenda de Citas | Calendario FullCalendar, 6 estados |
| Consultas | Diagnóstico, tratamiento, signos vitales |
| Signos Vitales | Registro por consulta, IMC automático |
| Recetas | Digital (PDF A5) + Manuscrita (foto/scan) |
| Medicamentos | Sin precios — control de stock e inventario |
| Movimientos | Entradas/salidas con auditoría y firma |
| Solicitudes | Reposición de medicamentos |
| Reportes | 4 gráficas estadísticas (consultas, movimientos, medicamentos, géneros) |
| Nómina | Personal médico y administrativo |
| Usuarios | 7 roles con permisos granulares |
| Mensajes | Mensajería interna individual y masiva |
| Bitácora | Auditoría completa de acciones |
| Exportaciones | PDF, Excel y CSV |

---

## Documentación

| Documento | Descripción |
|---|---|
| `docs/manual_usuario.html` | Guía completa para usuarios finales |
| `docs/documentacion_tecnica.html` | Arquitectura, instalación, API de rutas |
| `docs/documentacion_codigo.html` | Código fuente, módulo por módulo |
| `docs/modelo_er.html` | Diagramas ER y UML interactivos |
| `database/sisgeclin_laravel.sql` | Dump completo de la base de datos |

---

## Stack tecnológico

| Componente | Versión |
|---|---|
| Laravel | 13 |
| PHP | 8.3+ |
| MariaDB/MySQL | 10.4+ / 8.0+ |
| mPDF | 8.3 |
| PhpSpreadsheet | 5.7 |
| Chart.js (local) | 4.4.3 |
| FullCalendar (local) | 6.1.11 |
| Boxicons (local) | 2.1.4 |
| Mermaid.js (CDN+fallback) | 10.x |
