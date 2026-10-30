-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-05-2026 a las 19:09:23
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sisgeclin_laravel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `accion` varchar(100) NOT NULL,
  `tabla` varchar(50) DEFAULT NULL,
  `registro_id` bigint(20) UNSIGNED DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bitacora`
--

INSERT INTO `bitacora` (`id`, `usuario_id`, `accion`, `tabla`, `registro_id`, `detalles`, `ip`, `created_at`) VALUES
(1, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 13:41:32'),
(2, 1, 'EDITAR USUARIO', 'usuarios', 2, 'Editó el usuario \'medico\' (contraseña cambiada)', '127.0.0.1', '2026-05-10 13:42:36'),
(3, 1, 'EDITAR USUARIO', 'usuarios', 3, 'Editó el usuario \'recepcion\' (contraseña cambiada)', '127.0.0.1', '2026-05-10 13:42:45'),
(4, 1, 'ENTRADA INVENTARIO', 'inventario_movimientos', 1, 'Entrada de 16 unidades de Amoxicilina 500mg', '127.0.0.1', '2026-05-10 13:50:27'),
(5, 1, 'ENTRADA INVENTARIO', 'inventario_movimientos', 2, 'Entrada de 700 unidades de Atorvastatina 20mg', '127.0.0.1', '2026-05-10 13:50:38'),
(6, 1, 'SALIDA INVENTARIO', 'inventario_movimientos', 3, 'Salida de 12 unidades de Amoxicilina 500mg', '127.0.0.1', '2026-05-10 13:52:25'),
(7, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 13:54:27'),
(8, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 13:54:42'),
(9, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 14:10:21'),
(10, 2, 'INICIO SESIÓN', 'usuarios', 2, NULL, '127.0.0.1', '2026-05-10 14:10:26'),
(11, 2, 'CIERRE SESIÓN', 'usuarios', 2, NULL, '127.0.0.1', '2026-05-10 14:11:23'),
(12, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 14:12:36'),
(13, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 14:15:58'),
(14, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 14:19:29'),
(15, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 14:20:01'),
(16, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 14:23:56'),
(17, 1, 'EDITAR USUARIO', 'usuarios', 4, 'Editó el usuario \'Blacke2f\'', '127.0.0.1', '2026-05-10 14:24:10'),
(18, 1, 'ACTIVAR USUARIO', 'usuarios', 4, 'activó el usuario \'Blacke2f\'', '127.0.0.1', '2026-05-10 14:24:13'),
(19, 1, 'SALIDA INVENTARIO', 'inventario_movimientos', 4, 'Salida de 30 unidades de Amoxicilina 700mg', '127.0.0.1', '2026-05-10 14:27:52'),
(20, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 14:31:09'),
(21, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 14:35:54'),
(22, 4, 'SALIDA INVENTARIO', 'inventario_movimientos', 5, 'Salida de 30 unidades de Amoxicilina 700mg', '127.0.0.1', '2026-05-10 14:37:01'),
(23, 4, 'SALIDA INVENTARIO', 'inventario_movimientos', 6, 'Salida de 10 unidades de Amoxicilina 700mg', '127.0.0.1', '2026-05-10 14:37:45'),
(24, 4, 'CIERRE SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 14:53:40'),
(25, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 15:03:41'),
(26, 4, 'CIERRE SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 15:23:18'),
(27, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '192.168.1.2', '2026-05-10 15:26:41'),
(28, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '192.168.1.2', '2026-05-10 15:43:08'),
(29, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '192.168.1.2', '2026-05-10 15:43:16'),
(30, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 16:45:58'),
(31, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 16:46:03'),
(32, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 16:46:14'),
(33, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 16:46:24'),
(34, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 16:46:47'),
(35, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 16:47:20'),
(36, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 16:53:17'),
(37, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 16:55:06'),
(38, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 16:55:19'),
(39, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 16:55:53'),
(40, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '192.168.1.2', '2026-05-10 16:56:42'),
(41, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 16:56:55'),
(42, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '192.168.1.2', '2026-05-10 16:57:14'),
(43, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 16:57:32'),
(44, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 16:57:40'),
(45, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 16:58:10'),
(46, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 16:58:28'),
(47, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 16:59:01'),
(48, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 17:05:03'),
(49, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '192.168.1.2', '2026-05-10 17:05:31'),
(50, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '192.168.1.2', '2026-05-10 17:07:52'),
(51, 4, 'CIERRE SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-10 17:08:18'),
(52, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 17:17:17'),
(53, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '192.168.1.2', '2026-05-10 17:19:08'),
(54, 4, 'CIERRE SESIÓN', 'usuarios', 4, NULL, '192.168.1.2', '2026-05-10 17:23:03'),
(55, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '192.168.1.2', '2026-05-10 17:23:35'),
(56, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 17:58:43'),
(57, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 18:08:38'),
(58, 4, 'CIERRE SESIÓN', 'usuarios', 4, NULL, '192.168.1.2', '2026-05-10 18:18:33'),
(59, 1, 'EDITAR EMPLEADO', 'nomina', 1, 'Editó el empleado \'Roberto Méndez\'', '127.0.0.1', '2026-05-10 18:27:10'),
(60, 1, 'EDITAR EMPLEADO', 'nomina', 1, 'Editó el empleado \'Roberto Méndez\'', '127.0.0.1', '2026-05-10 18:27:27'),
(61, 1, 'EDITAR EMPLEADO', 'nomina', 3, 'Editó el empleado \'Luis Castillo\'', '127.0.0.1', '2026-05-10 18:27:46'),
(62, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-10 18:28:18'),
(63, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-11 17:44:36'),
(64, 1, 'SALIDA INVENTARIO', 'inventario_movimientos', 7, 'Salida de 30 unidades de Ibuprofeno 400mg', '127.0.0.1', '2026-05-11 17:59:16'),
(65, 1, 'ENTRADA INVENTARIO', 'inventario_movimientos', 8, 'Entrada de 20 unidades de Amoxicilina 700mg', '::1', '2026-05-11 19:09:11'),
(66, 1, 'ENTRADA INVENTARIO', 'inventario_movimientos', 9, 'Entrada de 25 unidades de Ibuprofeno 400mg', '::1', '2026-05-11 19:09:31'),
(67, 1, 'ENTRADA INVENTARIO', 'inventario_movimientos', 10, 'Entrada de 25 unidades de Atorvastatina 20mg', '::1', '2026-05-11 19:09:49'),
(68, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-11 19:10:34'),
(69, 2, 'INICIO SESIÓN', 'usuarios', 2, NULL, '::1', '2026-05-11 19:10:46'),
(70, 2, 'CIERRE SESIÓN', 'usuarios', 2, NULL, '::1', '2026-05-11 19:11:12'),
(71, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '127.0.0.1', '2026-05-11 19:22:31'),
(72, 1, 'SALIDA INVENTARIO', 'inventario_movimientos', 11, 'Salida de 70 unidades de Losartán 50mg', '127.0.0.1', '2026-05-11 19:32:18'),
(73, 1, 'ENTRADA INVENTARIO', 'inventario_movimientos', 12, 'Entrada de 40 unidades de Losartán 50mg', '127.0.0.1', '2026-05-11 19:33:24'),
(74, 1, 'CREAR USUARIO', 'usuarios', 5, 'Creó el usuario \'coquerokli\' con rol medico', '127.0.0.1', '2026-05-11 19:54:49'),
(75, 1, 'DESACTIVAR USUARIO', 'usuarios', 5, 'desactivó el usuario \'coquerokli\'', '127.0.0.1', '2026-05-11 19:55:01'),
(76, 1, 'ACTIVAR USUARIO', 'usuarios', 5, 'activó el usuario \'coquerokli\'', '127.0.0.1', '2026-05-11 19:55:04'),
(77, 1, 'ELIMINAR USUARIO', 'usuarios', 5, 'Eliminó el usuario \'coquerokli\'', '127.0.0.1', '2026-05-11 19:58:53'),
(78, 1, 'EDITAR EMPLEADO', 'nomina', 1, 'Editó el empleado \'Roberto Méndez\'', '127.0.0.1', '2026-05-11 20:10:06'),
(79, 1, 'EDITAR EMPLEADO', 'nomina', 1, 'Editó el empleado \'Roberto Méndez\'', '127.0.0.1', '2026-05-11 20:10:19'),
(80, 1, 'CREAR EMPLEADO', 'nomina', 7, 'Creó el empleado \'enzo Colmenarez\'', '127.0.0.1', '2026-05-11 20:10:46'),
(81, 1, 'CREAR USUARIO', 'usuarios', 6, 'Creó el usuario \'coquerokli\' con rol medico', '127.0.0.1', '2026-05-11 20:13:40'),
(82, 1, 'DESACTIVAR USUARIO', 'usuarios', 4, 'desactivó el usuario \'Blacke2f\'', '127.0.0.1', '2026-05-11 20:14:41'),
(83, 1, 'ACTIVAR USUARIO', 'usuarios', 4, 'activó el usuario \'Blacke2f\'', '127.0.0.1', '2026-05-11 20:14:45'),
(84, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-11 20:23:56'),
(85, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-11 20:29:31'),
(86, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-11 20:30:06'),
(87, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-11 20:30:27'),
(88, 1, 'SALIDA INVENTARIO', 'inventario_movimientos', 13, 'Salida de 5 unidades de Amoxicilina 700mg', '::1', '2026-05-11 20:31:15'),
(89, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-11 20:31:46'),
(90, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-12 23:05:49'),
(91, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-12 23:12:06'),
(92, 1, 'INICIO SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-12 23:12:09'),
(93, 1, 'SALIDA INVENTARIO', 'inventario_movimientos', 14, 'Salida de 10 unidades de Ibuprofeno 400mg', '::1', '2026-05-12 23:35:28'),
(94, 1, 'ENTRADA INVENTARIO', 'inventario_movimientos', 15, 'Entrada de 50 unidades de Ibuprofeno 400mg', '::1', '2026-05-12 23:38:52'),
(95, 1, 'SALIDA INVENTARIO', 'inventario_movimientos', 16, 'Salida de 15 unidades de Amoxicilina 700mg', '::1', '2026-05-13 00:31:01'),
(96, 1, 'CIERRE SESIÓN', 'usuarios', 1, NULL, '::1', '2026-05-13 00:31:29'),
(97, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '::1', '2026-05-13 00:31:39'),
(98, 4, 'ENTRADA INVENTARIO', 'inventario_movimientos', 17, 'Entrada de 10 unidades de Amoxicilina 700mg', '::1', '2026-05-13 00:32:04'),
(99, 4, 'CIERRE SESIÓN', 'usuarios', 4, NULL, '::1', '2026-05-13 01:08:10'),
(100, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-13 12:40:28'),
(101, 4, 'CIERRE SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-13 13:06:34'),
(102, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-13 14:22:28'),
(103, 4, 'EDITAR PERMISOS', 'usuarios', 6, 'Actualizó los permisos del usuario \'coquerokli\'', '127.0.0.1', '2026-05-13 14:31:19'),
(104, 4, 'CIERRE SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-13 14:31:37'),
(107, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-13 14:34:00'),
(108, 4, 'EDITAR PERMISOS', 'usuarios', 3, 'Actualizó los permisos del usuario \'recepcion\'', '127.0.0.1', '2026-05-13 14:35:59'),
(109, 4, 'EDITAR PERMISOS', 'usuarios', 6, 'Actualizó los permisos del usuario \'coquerokli\'', '127.0.0.1', '2026-05-13 14:37:18'),
(110, 4, 'EDITAR USUARIO', 'usuarios', 4, 'Editó el usuario \'Blacke2f\'', '127.0.0.1', '2026-05-13 14:37:30'),
(111, 4, 'ELIMINAR USUARIO', 'usuarios', 6, 'Eliminó el usuario \'coquerokli\'', '127.0.0.1', '2026-05-13 15:21:15'),
(112, 4, 'CREAR USUARIO', 'usuarios', 7, 'Creó el usuario \'coquerokli\' con rol recepcionista', '127.0.0.1', '2026-05-13 15:21:33'),
(113, 4, 'CIERRE SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-13 15:21:45'),
(114, 7, 'INICIO SESIÓN', 'usuarios', 7, NULL, '127.0.0.1', '2026-05-13 15:21:48'),
(115, 7, 'CIERRE SESIÓN', 'usuarios', 7, NULL, '127.0.0.1', '2026-05-13 15:21:58'),
(116, 7, 'INICIO SESIÓN', 'usuarios', 7, NULL, '127.0.0.1', '2026-05-13 15:22:04'),
(117, 7, 'CIERRE SESIÓN', 'usuarios', 7, NULL, '127.0.0.1', '2026-05-13 15:22:18'),
(118, 7, 'INICIO SESIÓN', 'usuarios', 7, NULL, '127.0.0.1', '2026-05-13 15:22:25'),
(119, 7, 'CIERRE SESIÓN', 'usuarios', 7, NULL, '127.0.0.1', '2026-05-13 15:22:29'),
(120, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-13 15:22:33'),
(121, 4, 'INICIO SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-13 16:01:17'),
(122, 4, 'EDITAR EMPLEADO', 'nomina', 7, 'Editó el empleado \'enzo Colmenarez\'', '127.0.0.1', '2026-05-13 17:08:25'),
(123, 4, 'CIERRE SESIÓN', 'usuarios', 4, NULL, '127.0.0.1', '2026-05-13 17:09:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas`
--

CREATE TABLE `consultas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `paciente_id` bigint(20) UNSIGNED NOT NULL,
  `medico_id` bigint(20) UNSIGNED DEFAULT NULL,
  `empleado_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_hora` datetime NOT NULL,
  `motivo` text NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `tratamiento` text DEFAULT NULL,
  `estado` enum('pendiente','en_proceso','completada','cancelada') NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `consultas`
--

INSERT INTO `consultas` (`id`, `paciente_id`, `medico_id`, `empleado_id`, `fecha_hora`, `motivo`, `diagnostico`, `tratamiento`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 1, '2026-04-30 23:01:22', 'Control de presión arterial', 'Hipertensión controlada', NULL, 'completada', '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(2, 2, NULL, 1, '2026-05-02 23:01:22', 'Dolor abdominal', 'Gastritis aguda', NULL, 'completada', '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(3, 3, NULL, 5, '2026-05-04 23:01:22', 'Fiebre y malestar general', 'Infección viral', NULL, 'completada', '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(4, 1, NULL, 6, '2026-05-06 23:01:00', 'Seguimiento de tratamiento cardiovascular', 'Control de arritmia', NULL, 'cancelada', '2026-05-10 17:28:34', '2026-05-13 18:38:03'),
(5, 2, NULL, 1, '2026-05-11 13:28:00', 'Consulta de rutina', NULL, NULL, 'completada', '2026-05-10 17:28:34', '2026-05-12 00:03:43'),
(6, 2, NULL, 6, '2026-05-10 10:10:00', 'Dolor en el pecho', NULL, NULL, 'completada', '2026-05-10 18:11:13', '2026-05-12 00:30:51'),
(7, 6, NULL, 6, '2026-05-11 10:28:00', 'Problemas de tensión.', NULL, NULL, 'completada', '2026-05-10 18:29:12', '2026-05-12 00:14:11'),
(8, 3, NULL, 6, '2026-05-11 10:50:00', 'Emergencia', NULL, NULL, 'completada', '2026-05-10 18:51:37', '2026-05-12 00:08:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas_medicamentos`
--

CREATE TABLE `consultas_medicamentos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `consulta_id` bigint(20) UNSIGNED NOT NULL,
  `medicamento_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `indicaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_medicamento` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `unidad` varchar(30) NOT NULL DEFAULT 'unidades',
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha_vencimiento` date DEFAULT NULL,
  `proveedor` varchar(150) DEFAULT NULL,
  `telefono_proveedor` varchar(30) DEFAULT NULL,
  `stock_minimo` int(11) NOT NULL DEFAULT 10,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `nombre_medicamento`, `descripcion`, `cantidad`, `unidad`, `precio`, `fecha_vencimiento`, `proveedor`, `telefono_proveedor`, `stock_minimo`, `created_at`, `updated_at`) VALUES
(1, 'Paracetamol 500mg', 'Analgésico y antipirético', 200, 'tabletas', 0.50, '2026-12-31', 'Farmacéutica Nacional', NULL, 50, '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(2, 'Amoxicilina 700mg', 'Antibiótico de amplio espectro', 25, 'cápsulas', 1.20, '2028-10-10', 'MedSupply', NULL, 20, '2026-05-10 17:28:34', '2026-05-13 04:32:04'),
(3, 'Ibuprofeno 400mg', 'Antiinflamatorio no esteroideo', 80, 'tabletas', 0.75, '2027-10-15', 'Farmacéutica Nacional', NULL, 30, '2026-05-10 17:28:34', '2026-05-13 03:38:52'),
(4, 'Omeprazol 20mg', 'Inhibidor de la bomba de protones', 120, 'cápsulas', 0.80, '2026-08-20', 'MedSupply', NULL, 40, '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(5, 'Losartán 50mg', 'Antihipertensivo', 60, 'tabletas', 1.50, '2026-10-15', 'Farmacéutica Nacional', NULL, 30, '2026-05-10 17:28:34', '2026-05-11 23:33:24'),
(6, 'Metformina 850mg', 'Antidiabético oral', 150, 'tabletas', 0.60, '2026-11-30', 'MedSupply', NULL, 50, '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(7, 'Atorvastatina 20mg', 'Hipolipemiante', 45, 'tabletas', 2.00, NULL, 'Farmacéutica Nacional', NULL, 25, '2026-05-10 17:28:34', '2026-05-11 23:09:49'),
(8, 'Salbutamol 100mcg', 'Broncodilatador', 45, 'inhaladores', 3.50, '2026-07-15', 'MedSupply', NULL, 15, '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(9, 'losartan', 'Medicamento para la atención', 50, 'tabletas', 0.08, '2029-02-01', 'Farmacéutica Nacional', NULL, 10, '2026-05-12 00:09:43', '2026-05-12 00:09:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_movimientos`
--

CREATE TABLE `inventario_movimientos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `medicamento_id` bigint(20) UNSIGNED NOT NULL,
  `tipo_movimiento` enum('entrada','salida') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `cantidad_anterior` int(11) NOT NULL,
  `cantidad_nueva` int(11) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `telefono_proveedor` varchar(30) DEFAULT NULL,
  `consulta_id` bigint(20) UNSIGNED DEFAULT NULL,
  `paciente_id` bigint(20) UNSIGNED DEFAULT NULL,
  `entregado_por_nombre` varchar(150) DEFAULT NULL,
  `recibido_por_nombre` varchar(150) DEFAULT NULL,
  `recibido_por_cedula` varchar(20) DEFAULT NULL,
  `observaciones_entrega` text DEFAULT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inventario_movimientos`
--

INSERT INTO `inventario_movimientos` (`id`, `medicamento_id`, `tipo_movimiento`, `cantidad`, `cantidad_anterior`, `cantidad_nueva`, `motivo`, `telefono_proveedor`, `consulta_id`, `paciente_id`, `entregado_por_nombre`, `recibido_por_nombre`, `recibido_por_cedula`, `observaciones_entrega`, `usuario_id`, `created_at`) VALUES
(1, 2, 'entrada', 16, 80, 96, 'Entrada de inventario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-10 13:50:27'),
(2, 7, 'entrada', 700, 33, 733, 'Entrada de inventario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-10 13:50:38'),
(3, 2, 'salida', 12, 96, 84, 'Salida de inventario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-10 13:52:25'),
(4, 2, 'salida', 30, 85, 55, 'Salida de inventario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-10 14:27:51'),
(5, 2, 'salida', 30, 55, 25, 'Salida de inventario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, '2026-05-10 14:37:01'),
(6, 2, 'salida', 10, 25, 15, 'Salida de inventario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, '2026-05-10 14:37:45'),
(7, 3, 'salida', 30, 45, 15, 'Salida de inventario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-11 17:59:16'),
(8, 2, 'entrada', 20, 15, 35, 'Entrada de inventario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-11 19:09:11'),
(9, 3, 'entrada', 25, 15, 40, 'Entrada de inventario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-11 19:09:31'),
(10, 7, 'entrada', 25, 20, 45, 'Entrada de inventario', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-11 19:09:49'),
(11, 5, 'salida', 70, 90, 20, 'Salida de inventario', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, '2026-05-11 19:32:18'),
(12, 5, 'entrada', 40, 20, 60, 'Entrada por solicitud SOL-202605-0001 — Factura: 2506', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-11 19:33:24'),
(13, 2, 'salida', 5, 35, 30, 'Salida de inventario', NULL, NULL, 3, NULL, NULL, NULL, NULL, 1, '2026-05-11 20:31:15'),
(14, 3, 'salida', 10, 40, 30, 'Salida de inventario', NULL, NULL, 6, NULL, NULL, NULL, NULL, 1, '2026-05-12 23:35:28'),
(15, 3, 'entrada', 50, 30, 80, 'Entrada por solicitud SOL-202605-0002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-12 23:38:52'),
(16, 2, 'salida', 15, 30, 15, 'Salida de inventario', NULL, NULL, 2, NULL, NULL, NULL, NULL, 1, '2026-05-13 00:31:01'),
(17, 2, 'entrada', 10, 15, 25, 'Entrada por solicitud SOL-202605-0003 — Factura: 25060', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, '2026-05-13 00:32:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `remitente_id` bigint(20) UNSIGNED NOT NULL,
  `destinatario_id` bigint(20) UNSIGNED DEFAULT NULL,
  `asunto` varchar(200) NOT NULL,
  `contenido` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `es_masivo` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `remitente_id`, `destinatario_id`, `asunto`, `contenido`, `leido`, `es_masivo`, `created_at`) VALUES
(1, 1, 2, 'Bienvenido al sistema', 'Bienvenido al sistema de gestión clínica SISGECLIN.', 1, 0, '2026-05-10 17:28:34'),
(2, 1, 3, 'Recordatorio de reunión', 'Recuerde la reunión de personal mañana a las 9:00 AM.', 1, 0, '2026-05-10 17:28:34'),
(3, 2, 1, 'Solicitud de medicamentos', 'Necesitamos reabastecer el inventario de Ibuprofeno.', 1, 0, '2026-05-10 17:28:34'),
(7, 1, 4, 'Hola', 'ee', 1, 0, '2026-05-10 17:19:29'),
(8, 4, 1, 'Reunión a las 15:00 H de la tarde.', 'Las reuniones es para saber dónde están los medicamentos solicitados.', 0, 1, '2026-05-13 00:34:06'),
(9, 4, 2, 'Reunión a las 15:00 H de la tarde.', 'Las reuniones es para saber dónde están los medicamentos solicitados.', 0, 1, '2026-05-13 00:34:06'),
(10, 4, 3, 'Reunión a las 15:00 H de la tarde.', 'Las reuniones es para saber dónde están los medicamentos solicitados.', 0, 1, '2026-05-13 00:34:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_01_000001_create_pacientes_table', 1),
(5, '2026_01_01_000002_create_nomina_table', 1),
(6, '2026_01_01_000003_create_inventario_table', 1),
(7, '2026_01_01_000004_create_usuarios_table', 1),
(8, '2026_01_01_000005_create_consultas_table', 1),
(9, '2026_01_01_000006_create_inventario_movimientos_table', 1),
(10, '2026_01_01_000007_create_mensajes_table', 1),
(11, '2026_01_01_000008_create_bitacora_table', 1),
(12, '2026_01_01_000009_create_consultas_medicamentos_table', 1),
(13, '2026_01_01_000010_create_notificaciones_table', 2),
(14, '2026_01_02_000001_add_fields_to_inventario_movimientos', 3),
(15, '2026_01_02_000002_create_solicitudes_inventario_table', 3),
(16, '2026_01_02_000003_create_solicitud_items_table', 4),
(17, '2026_01_02_000004_add_numero_orden_to_solicitudes', 5),
(18, '2026_01_03_000001_add_firma_fields_to_inventario_movimientos', 6),
(19, '2026_01_04_000001_create_permisos_usuario_table', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomina`
--

CREATE TABLE `nomina` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `departamento` varchar(100) NOT NULL,
  `salario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha_ingreso` date NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `nomina`
--

INSERT INTO `nomina` (`id`, `nombre`, `apellido`, `cedula`, `cargo`, `departamento`, `salario`, `fecha_ingreso`, `telefono`, `email`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Roberto', 'Méndez', '101-0001234-5', 'Médico General', 'Consultas', 85000.00, '2000-10-20', '809-555-1001', 'rmendez@sisgeclin.com', 'activo', '2026-05-10 17:28:33', '2026-05-12 00:10:19'),
(2, 'Ana', 'Torres', '102-0005678-9', 'Enfermera Jefe', 'Enfermería', 45000.00, '2019-06-01', '809-555-1002', 'atorres@sisgeclin.com', 'activo', '2026-05-10 17:28:33', '2026-05-10 17:28:33'),
(3, 'Luis', 'Castillo', '103-0009012-3', 'Recepcionista', 'Administración', 25000.00, '2026-05-10', '809-555-1003', 'lcastillo@sisgeclin.com', 'activo', '2026-05-10 17:28:33', '2026-05-10 22:27:46'),
(4, 'Carmen', 'Díaz', '104-0003456-7', 'Farmacéutica', 'Farmacia', 38000.00, '2020-08-20', '809-555-1004', 'cdiaz@sisgeclin.com', 'activo', '2026-05-10 17:28:33', '2026-05-10 17:28:33'),
(5, 'José', 'Ramírez', '105-0007890-1', 'Pediatra', 'Consultas', 90000.00, '2021-02-01', '809-555-1005', 'jramirez@sisgeclin.com', 'activo', '2026-05-10 17:28:33', '2026-05-10 17:28:33'),
(6, 'Laura', 'Fernández', '106-0004567-8', 'Cardiólogo', 'Consultas', 95000.00, '2020-05-15', '809-555-1006', 'lfernandez@sisgeclin.com', 'activo', '2026-05-10 17:28:33', '2026-05-10 17:28:33'),
(7, 'enzo', 'Colmenarez', '28528561', 'Médico General', 'Administración', 3000.00, '2000-10-10', '04262661241', 'Enzoeliud1010@gmail.com', 'activo', '2026-05-12 00:10:46', '2026-05-13 21:08:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `icono` varchar(50) NOT NULL DEFAULT 'bx-bell',
  `color` varchar(20) NOT NULL DEFAULT 'primary',
  `url` varchar(255) DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `usuario_id`, `tipo`, `titulo`, `mensaje`, `icono`, `color`, `url`, `leida`, `created_at`) VALUES
(1, 1, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para María González — 10/05/2026 10:10', 'bx-clipboard', 'primary', '/consultas', 1, '2026-05-10 18:11:13'),
(2, 3, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para María González — 10/05/2026 10:10', 'bx-clipboard', 'primary', '/consultas', 0, '2026-05-10 18:11:13'),
(3, 1, 'registro_pendiente', 'Nueva Solicitud de Acceso', 'Enzo Colmenarez (Blacke2f) solicitó acceso como Medico', 'bx-user-plus', 'warning', '/usuarios', 1, '2026-05-10 14:23:42'),
(4, 2, 'paciente_nuevo', 'Nuevo Paciente Registrado', 'Se registró el paciente LAURA JOSEFINA NOGUERA (Cédula: 13189929)', 'bxs-user-plus', 'success', '/pacientes', 1, '2026-05-10 18:25:24'),
(5, 3, 'paciente_nuevo', 'Nuevo Paciente Registrado', 'Se registró el paciente LAURA JOSEFINA NOGUERA (Cédula: 13189929)', 'bxs-user-plus', 'success', '/pacientes', 0, '2026-05-10 18:25:24'),
(6, 4, 'paciente_nuevo', 'Nuevo Paciente Registrado', 'Se registró el paciente LAURA JOSEFINA NOGUERA (Cédula: 13189929)', 'bxs-user-plus', 'success', '/pacientes', 1, '2026-05-10 18:25:24'),
(7, 2, 'salida_inventario', 'Salida de Inventario', '-30 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-10 18:27:52'),
(8, 3, 'salida_inventario', 'Salida de Inventario', '-30 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-10 18:27:52'),
(9, 4, 'salida_inventario', 'Salida de Inventario', '-30 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-10 18:27:52'),
(10, 2, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para LAURA JOSEFINA NOGUERA — 10/05/2026 10:28', 'bx-clipboard', 'primary', '/consultas', 1, '2026-05-10 18:29:12'),
(11, 3, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para LAURA JOSEFINA NOGUERA — 10/05/2026 10:28', 'bx-clipboard', 'primary', '/consultas', 0, '2026-05-10 18:29:12'),
(12, 4, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para LAURA JOSEFINA NOGUERA — 10/05/2026 10:28', 'bx-clipboard', 'primary', '/consultas', 1, '2026-05-10 18:29:12'),
(13, 1, 'test', 'Test', 'Mensaje test', 'bx-bell', 'primary', NULL, 1, '2026-05-10 18:29:55'),
(14, 2, 'test', 'Test', 'Mensaje test', 'bx-bell', 'primary', NULL, 1, '2026-05-10 18:29:55'),
(15, 3, 'test', 'Test', 'Mensaje test', 'bx-bell', 'primary', NULL, 0, '2026-05-10 18:29:55'),
(16, 4, 'test', 'Test', 'Mensaje test', 'bx-bell', 'primary', NULL, 1, '2026-05-10 18:29:55'),
(17, 1, 'salida_inventario', 'Salida de Inventario', '-30 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-10 18:37:01'),
(18, 2, 'salida_inventario', 'Salida de Inventario', '-30 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-10 18:37:01'),
(19, 3, 'salida_inventario', 'Salida de Inventario', '-30 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-10 18:37:01'),
(20, 1, 'salida_inventario', 'Salida de Inventario', '-10 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-10 18:37:45'),
(21, 2, 'salida_inventario', 'Salida de Inventario', '-10 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-10 18:37:45'),
(22, 3, 'salida_inventario', 'Salida de Inventario', '-10 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-10 18:37:45'),
(23, 1, 'stock_bajo', 'Alerta: Stock Bajo', 'Amoxicilina 700mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-10 18:37:45'),
(24, 2, 'stock_bajo', 'Alerta: Stock Bajo', 'Amoxicilina 700mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-10 18:37:45'),
(25, 3, 'stock_bajo', 'Alerta: Stock Bajo', 'Amoxicilina 700mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 0, '2026-05-10 18:37:45'),
(26, 4, 'stock_bajo', 'Alerta: Stock Bajo', 'Amoxicilina 700mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-10 18:37:45'),
(27, 1, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para Carlos Rodríguez — 10/05/2026 10:50', 'bx-clipboard', 'primary', '/consultas', 1, '2026-05-10 18:51:37'),
(28, 2, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para Carlos Rodríguez — 10/05/2026 10:50', 'bx-clipboard', 'primary', '/consultas', 1, '2026-05-10 18:51:37'),
(29, 3, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para Carlos Rodríguez — 10/05/2026 10:50', 'bx-clipboard', 'primary', '/consultas', 0, '2026-05-10 18:51:37'),
(30, 4, 'mensaje_nuevo', 'Nuevo Mensaje', 'Administrador Sistema te envió: Hola', 'bxs-envelope', 'info', '/mensajes', 1, '2026-05-10 17:06:03'),
(31, 1, 'mensaje_nuevo', 'Nuevo Mensaje', 'Enzo Colmenarez te envió: Hola', 'bxs-envelope', 'info', '/mensajes', 1, '2026-05-10 17:06:39'),
(32, 4, 'mensaje_nuevo', 'Nuevo Mensaje', 'Administrador Sistema te envió: Hola', 'bxs-envelope', 'info', '/mensajes', 1, '2026-05-10 17:19:29'),
(33, 2, 'salida_inventario', 'Salida de Inventario', '-30 unidades de Ibuprofeno 400mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-11 21:59:16'),
(34, 3, 'salida_inventario', 'Salida de Inventario', '-30 unidades de Ibuprofeno 400mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-11 21:59:16'),
(35, 4, 'salida_inventario', 'Salida de Inventario', '-30 unidades de Ibuprofeno 400mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-11 21:59:16'),
(36, 1, 'stock_bajo', 'Alerta: Stock Bajo', 'Ibuprofeno 400mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-11 21:59:16'),
(37, 2, 'stock_bajo', 'Alerta: Stock Bajo', 'Ibuprofeno 400mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-11 21:59:16'),
(38, 3, 'stock_bajo', 'Alerta: Stock Bajo', 'Ibuprofeno 400mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 0, '2026-05-11 21:59:16'),
(39, 4, 'stock_bajo', 'Alerta: Stock Bajo', 'Ibuprofeno 400mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-11 21:59:16'),
(40, 1, 'solicitud_inventario', 'Nueva Solicitud de Reposición', 'Administrador Sistema solicitó reposición de 3 medicamento(s)', 'bx-package', 'warning', '/solicitudes', 1, '2026-05-11 22:41:57'),
(41, 4, 'solicitud_inventario', 'Nueva Solicitud de Reposición', 'Administrador Sistema solicitó reposición de 3 medicamento(s)', 'bx-package', 'warning', '/solicitudes', 1, '2026-05-11 22:41:57'),
(42, 1, 'solicitud_atendida', 'Solicitud Aprobada', 'Tu solicitud de Amoxicilina 700mg fue aprobada', 'bx-check-circle', 'success', '/inventario', 1, '2026-05-11 18:42:33'),
(43, 1, 'solicitud_atendida', 'Solicitud Aprobada', 'Tu solicitud de Ibuprofeno 400mg fue aprobada', 'bx-check-circle', 'success', '/inventario', 1, '2026-05-11 18:42:38'),
(44, 1, 'solicitud_atendida', 'Solicitud Aprobada', 'Tu solicitud de Atorvastatina 20mg fue aprobada', 'bx-check-circle', 'success', '/inventario', 1, '2026-05-11 18:42:43'),
(45, 1, 'solicitud_inventario', 'Nueva Solicitud de Reposición', 'Administrador Sistema solicitó reposición de 1 medicamento(s)', 'bx-package', 'warning', '/solicitudes', 1, '2026-05-11 23:08:00'),
(46, 4, 'solicitud_inventario', 'Nueva Solicitud de Reposición', 'Administrador Sistema solicitó reposición de 1 medicamento(s)', 'bx-package', 'warning', '/solicitudes', 1, '2026-05-11 23:08:00'),
(47, 1, 'solicitud_atendida', 'Solicitud Completada', 'Tu solicitud de Amoxicilina 700mg fue completada', 'bx-x-circle', 'danger', '/inventario', 1, '2026-05-11 19:08:12'),
(48, 2, 'entrada_inventario', 'Entrada de Inventario', '+20 unidades de Amoxicilina 700mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 1, '2026-05-11 23:09:11'),
(49, 3, 'entrada_inventario', 'Entrada de Inventario', '+20 unidades de Amoxicilina 700mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 0, '2026-05-11 23:09:11'),
(50, 4, 'entrada_inventario', 'Entrada de Inventario', '+20 unidades de Amoxicilina 700mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 1, '2026-05-11 23:09:11'),
(51, 2, 'entrada_inventario', 'Entrada de Inventario', '+25 unidades de Ibuprofeno 400mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 1, '2026-05-11 23:09:31'),
(52, 3, 'entrada_inventario', 'Entrada de Inventario', '+25 unidades de Ibuprofeno 400mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 0, '2026-05-11 23:09:31'),
(53, 4, 'entrada_inventario', 'Entrada de Inventario', '+25 unidades de Ibuprofeno 400mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 1, '2026-05-11 23:09:31'),
(54, 2, 'entrada_inventario', 'Entrada de Inventario', '+25 unidades de Atorvastatina 20mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 1, '2026-05-11 23:09:49'),
(55, 3, 'entrada_inventario', 'Entrada de Inventario', '+25 unidades de Atorvastatina 20mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 0, '2026-05-11 23:09:49'),
(56, 4, 'entrada_inventario', 'Entrada de Inventario', '+25 unidades de Atorvastatina 20mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 1, '2026-05-11 23:09:49'),
(57, 2, 'salida_inventario', 'Salida de Inventario', '-70 unidades de Losartán 50mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-11 23:32:18'),
(58, 3, 'salida_inventario', 'Salida de Inventario', '-70 unidades de Losartán 50mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-11 23:32:18'),
(59, 4, 'salida_inventario', 'Salida de Inventario', '-70 unidades de Losartán 50mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-11 23:32:18'),
(60, 1, 'stock_bajo', 'Alerta: Stock Bajo', 'Losartán 50mg tiene stock bajo (20 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-11 23:32:18'),
(61, 2, 'stock_bajo', 'Alerta: Stock Bajo', 'Losartán 50mg tiene stock bajo (20 unidades)', 'bx-error', 'danger', '/medicina', 0, '2026-05-11 23:32:18'),
(62, 3, 'stock_bajo', 'Alerta: Stock Bajo', 'Losartán 50mg tiene stock bajo (20 unidades)', 'bx-error', 'danger', '/medicina', 0, '2026-05-11 23:32:18'),
(63, 4, 'stock_bajo', 'Alerta: Stock Bajo', 'Losartán 50mg tiene stock bajo (20 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-11 23:32:18'),
(64, 1, 'solicitud_inventario', 'Nueva Solicitud de Reposición', 'Administrador Sistema solicitó reposición de 1 medicamento(s)', 'bx-package', 'warning', '/solicitudes', 1, '2026-05-11 23:32:31'),
(65, 4, 'solicitud_inventario', 'Nueva Solicitud de Reposición', 'Administrador Sistema solicitó reposición de 1 medicamento(s)', 'bx-package', 'warning', '/solicitudes', 1, '2026-05-11 23:32:31'),
(66, 1, 'solicitud_atendida', 'Solicitud Aprobada', 'Tu solicitud de Losartán 50mg fue aprobada', 'bx-check-circle', 'success', '/solicitudes', 1, '2026-05-11 19:33:24'),
(67, 2, 'entrada_inventario', 'Entrada de Inventario', '+40 unidades de Losartán 50mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 0, '2026-05-11 23:33:24'),
(68, 3, 'entrada_inventario', 'Entrada de Inventario', '+40 unidades de Losartán 50mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 0, '2026-05-11 23:33:24'),
(69, 4, 'entrada_inventario', 'Entrada de Inventario', '+40 unidades de Losartán 50mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 1, '2026-05-11 23:33:24'),
(70, 1, 'solicitud_atendida', 'Solicitud Completada', 'Tu solicitud de Losartán 50mg fue completada', 'bx-x-circle', 'success', '/solicitudes', 1, '2026-05-11 19:33:32'),
(71, 1, 'solicitud_atendida', 'Solicitud Completada', 'Tu solicitud de Amoxicilina 700mg fue completada', 'bx-x-circle', 'success', '/solicitudes', 1, '2026-05-11 19:33:50'),
(72, 1, 'solicitud_atendida', 'Solicitud Completada', 'Tu solicitud de Ibuprofeno 400mg fue completada', 'bx-x-circle', 'success', '/solicitudes', 1, '2026-05-11 19:33:55'),
(73, 1, 'solicitud_atendida', 'Solicitud Completada', 'Tu solicitud de Atorvastatina 20mg fue completada', 'bx-x-circle', 'success', '/solicitudes', 1, '2026-05-11 19:34:14'),
(74, 2, 'paciente_nuevo', 'Nuevo Paciente Registrado', 'Se registró el paciente Juan colemnarez (Cédula: V-13189929)', 'bxs-user-plus', 'success', '/pacientes', 0, '2026-05-11 23:57:01'),
(75, 3, 'paciente_nuevo', 'Nuevo Paciente Registrado', 'Se registró el paciente Juan colemnarez (Cédula: V-13189929)', 'bxs-user-plus', 'success', '/pacientes', 0, '2026-05-11 23:57:01'),
(76, 4, 'paciente_nuevo', 'Nuevo Paciente Registrado', 'Se registró el paciente Juan colemnarez (Cédula: V-13189929)', 'bxs-user-plus', 'success', '/pacientes', 1, '2026-05-11 23:57:01'),
(78, 2, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para Juan Pérez — 11/05/2026 16:01', 'bx-clipboard', 'primary', '/consultas', 0, '2026-05-12 00:01:48'),
(79, 3, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para Juan Pérez — 11/05/2026 16:01', 'bx-clipboard', 'primary', '/consultas', 0, '2026-05-12 00:01:48'),
(80, 4, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para Juan Pérez — 11/05/2026 16:01', 'bx-clipboard', 'primary', '/consultas', 1, '2026-05-12 00:01:48'),
(81, 2, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para Ana Martínez — 11/05/2026 16:02', 'bx-clipboard', 'primary', '/consultas', 0, '2026-05-12 00:02:42'),
(82, 3, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para Ana Martínez — 11/05/2026 16:02', 'bx-clipboard', 'primary', '/consultas', 0, '2026-05-12 00:02:42'),
(83, 4, 'consulta_nueva', 'Nueva Consulta Registrada', 'Consulta para Ana Martínez — 11/05/2026 16:02', 'bx-clipboard', 'primary', '/consultas', 1, '2026-05-12 00:02:42'),
(84, 2, 'salida_inventario', 'Salida de Inventario', '-5 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-12 00:31:15'),
(85, 3, 'salida_inventario', 'Salida de Inventario', '-5 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-12 00:31:15'),
(86, 4, 'salida_inventario', 'Salida de Inventario', '-5 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-12 00:31:15'),
(88, 2, 'salida_inventario', 'Salida de Inventario', '-10 unidades de Ibuprofeno 400mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-13 03:35:28'),
(89, 3, 'salida_inventario', 'Salida de Inventario', '-10 unidades de Ibuprofeno 400mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-13 03:35:28'),
(90, 4, 'salida_inventario', 'Salida de Inventario', '-10 unidades de Ibuprofeno 400mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-13 03:35:28'),
(92, 1, 'stock_bajo', 'Alerta: Stock Bajo', 'Ibuprofeno 400mg tiene stock bajo (30 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-13 03:35:28'),
(93, 2, 'stock_bajo', 'Alerta: Stock Bajo', 'Ibuprofeno 400mg tiene stock bajo (30 unidades)', 'bx-error', 'danger', '/medicina', 0, '2026-05-13 03:35:28'),
(94, 3, 'stock_bajo', 'Alerta: Stock Bajo', 'Ibuprofeno 400mg tiene stock bajo (30 unidades)', 'bx-error', 'danger', '/medicina', 0, '2026-05-13 03:35:28'),
(95, 4, 'stock_bajo', 'Alerta: Stock Bajo', 'Ibuprofeno 400mg tiene stock bajo (30 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-13 03:35:28'),
(97, 1, 'solicitud_inventario', 'Nueva Solicitud de Reposición', 'Administrador Sistema solicitó reposición de 1 medicamento(s)', 'bx-package', 'warning', '/solicitudes', 1, '2026-05-13 03:37:13'),
(98, 4, 'solicitud_inventario', 'Nueva Solicitud de Reposición', 'Administrador Sistema solicitó reposición de 1 medicamento(s)', 'bx-package', 'warning', '/solicitudes', 1, '2026-05-13 03:37:13'),
(99, 1, 'solicitud_atendida', 'Solicitud Aprobada', 'Tu solicitud de Ibuprofeno 400mg fue aprobada', 'bx-check-circle', 'success', '/solicitudes', 1, '2026-05-12 23:38:52'),
(100, 2, 'entrada_inventario', 'Entrada de Inventario', '+50 unidades de Ibuprofeno 400mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 0, '2026-05-13 03:38:52'),
(101, 3, 'entrada_inventario', 'Entrada de Inventario', '+50 unidades de Ibuprofeno 400mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 0, '2026-05-13 03:38:52'),
(102, 4, 'entrada_inventario', 'Entrada de Inventario', '+50 unidades de Ibuprofeno 400mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 1, '2026-05-13 03:38:52'),
(104, 1, 'solicitud_atendida', 'Solicitud Completada', 'Tu solicitud de Ibuprofeno 400mg fue completada', 'bx-x-circle', 'success', '/solicitudes', 1, '2026-05-12 23:47:00'),
(105, 2, 'salida_inventario', 'Salida de Inventario', '-15 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-13 04:31:01'),
(106, 3, 'salida_inventario', 'Salida de Inventario', '-15 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 0, '2026-05-13 04:31:01'),
(107, 4, 'salida_inventario', 'Salida de Inventario', '-15 unidades de Amoxicilina 700mg retiradas del stock', 'bx-down-arrow-circle', 'warning', '/inventario', 1, '2026-05-13 04:31:01'),
(109, 1, 'stock_bajo', 'Alerta: Stock Bajo', 'Amoxicilina 700mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 0, '2026-05-13 04:31:01'),
(110, 2, 'stock_bajo', 'Alerta: Stock Bajo', 'Amoxicilina 700mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 0, '2026-05-13 04:31:01'),
(111, 3, 'stock_bajo', 'Alerta: Stock Bajo', 'Amoxicilina 700mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 0, '2026-05-13 04:31:01'),
(112, 4, 'stock_bajo', 'Alerta: Stock Bajo', 'Amoxicilina 700mg tiene stock bajo (15 unidades)', 'bx-error', 'danger', '/medicina', 1, '2026-05-13 04:31:01'),
(114, 1, 'solicitud_inventario', 'Nueva Solicitud de Reposición', 'Administrador Sistema solicitó reposición de 1 medicamento(s)', 'bx-package', 'warning', '/solicitudes', 0, '2026-05-13 04:31:25'),
(115, 4, 'solicitud_inventario', 'Nueva Solicitud de Reposición', 'Administrador Sistema solicitó reposición de 1 medicamento(s)', 'bx-package', 'warning', '/solicitudes', 1, '2026-05-13 04:31:25'),
(116, 1, 'solicitud_atendida', 'Solicitud Aprobada', 'Tu solicitud de Amoxicilina 700mg fue aprobada', 'bx-check-circle', 'success', '/solicitudes', 0, '2026-05-13 00:32:04'),
(117, 1, 'entrada_inventario', 'Entrada de Inventario', '+10 unidades de Amoxicilina 700mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 0, '2026-05-13 04:32:04'),
(118, 2, 'entrada_inventario', 'Entrada de Inventario', '+10 unidades de Amoxicilina 700mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 0, '2026-05-13 04:32:04'),
(119, 3, 'entrada_inventario', 'Entrada de Inventario', '+10 unidades de Amoxicilina 700mg ingresadas al stock', 'bx-up-arrow-circle', 'success', '/inventario', 0, '2026-05-13 04:32:04'),
(121, 1, 'solicitud_atendida', 'Solicitud Completada', 'Tu solicitud de Amoxicilina 700mg fue completada', 'bx-x-circle', 'success', '/solicitudes', 0, '2026-05-13 00:32:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('M','F','Otro') NOT NULL DEFAULT 'M',
  `tipo_sangre` varchar(5) DEFAULT NULL,
  `alergias` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`id`, `nombre`, `apellido`, `cedula`, `telefono`, `email`, `direccion`, `fecha_nacimiento`, `genero`, `tipo_sangre`, `alergias`, `created_at`, `updated_at`) VALUES
(1, 'Juan', 'Pérez', '001-1234567-8', '809-555-0101', 'juan@email.com', NULL, '1985-03-15', 'M', 'O+', NULL, '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(2, 'María', 'González', '002-9876543-1', '809-555-0202', 'maria@email.com', NULL, '1990-07-22', 'F', 'A+', NULL, '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(3, 'Carlos', 'Rodríguez', '003-4567890-2', '809-555-0303', NULL, NULL, '1978-11-08', 'M', 'B-', NULL, '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(4, 'Ana', 'Martínez', '004-1111111-1', '809-555-0404', 'ana@email.com', NULL, '1995-05-20', 'F', 'O+', NULL, '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(5, 'Pedro', 'López', '005-2222222-2', '809-555-0505', NULL, NULL, '1982-09-10', 'M', 'A-', NULL, '2026-05-10 17:28:34', '2026-05-10 17:28:34'),
(6, 'LAURA JOSEFINA', 'NOGUERA', '13189929', '+584160590962', 'reinaldo.apostol1@gmail.com', 'Manzanita está Lara..', '1971-10-06', 'F', 'A+', 'Alergia al polvo Y a las picaduras. De insectos.', '2026-05-10 18:25:24', '2026-05-10 18:25:24'),
(7, 'Juan', 'colemnarez', 'V-13189929', '04262556092', 'enzoeliud@homtail.com', 'Manzanita.', NULL, 'M', 'O+', 'Ninguna', '2026-05-11 23:57:01', '2026-05-12 00:30:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_usuario`
--

CREATE TABLE `permisos_usuario` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `puede_ver` tinyint(1) NOT NULL DEFAULT 0,
  `puede_crear` tinyint(1) NOT NULL DEFAULT 0,
  `puede_editar` tinyint(1) NOT NULL DEFAULT 0,
  `puede_eliminar` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos_usuario`
--

INSERT INTO `permisos_usuario` (`id`, `usuario_id`, `modulo`, `puede_ver`, `puede_crear`, `puede_editar`, `puede_eliminar`, `created_at`, `updated_at`) VALUES
(10, 3, 'pacientes', 1, 1, 1, 1, '2026-05-13 18:35:59', '2026-05-13 18:35:59'),
(11, 3, 'consultas', 1, 1, 1, 1, '2026-05-13 18:35:59', '2026-05-13 18:35:59'),
(12, 3, 'medicina', 1, 1, 1, 0, '2026-05-13 18:35:59', '2026-05-13 18:35:59'),
(13, 3, 'inventario', 1, 1, 0, 0, '2026-05-13 18:35:59', '2026-05-13 18:35:59'),
(14, 3, 'solicitudes', 1, 1, 1, 0, '2026-05-13 18:35:59', '2026-05-13 18:35:59'),
(15, 3, 'mensajes', 1, 1, 1, 1, '2026-05-13 18:35:59', '2026-05-13 18:35:59'),
(16, 3, 'nomina', 0, 0, 0, 0, '2026-05-13 18:35:59', '2026-05-13 18:35:59'),
(17, 3, 'usuarios', 0, 0, 0, 0, '2026-05-13 18:35:59', '2026-05-13 18:35:59'),
(18, 3, 'bitacora', 0, 0, 0, 0, '2026-05-13 18:35:59', '2026-05-13 18:35:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_inventario`
--

CREATE TABLE `solicitudes_inventario` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_orden` varchar(20) DEFAULT NULL,
  `medicamento_id` bigint(20) UNSIGNED NOT NULL,
  `solicitado_por` bigint(20) UNSIGNED NOT NULL,
  `cantidad_solicitada` int(11) NOT NULL,
  `proveedor_sugerido` varchar(150) DEFAULT NULL,
  `telefono_proveedor` varchar(30) DEFAULT NULL,
  `numero_factura` varchar(50) DEFAULT NULL,
  `fecha_entrega_esperada` date DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada','completada') NOT NULL DEFAULT 'pendiente',
  `atendida_por` bigint(20) UNSIGNED DEFAULT NULL,
  `atendida_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes_inventario`
--

INSERT INTO `solicitudes_inventario` (`id`, `numero_orden`, `medicamento_id`, `solicitado_por`, `cantidad_solicitada`, `proveedor_sugerido`, `telefono_proveedor`, `numero_factura`, `fecha_entrega_esperada`, `notas`, `estado`, `atendida_por`, `atendida_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 2, 1, 25, 'MedSupply', NULL, NULL, NULL, 'Samur medicamento común y que salen.', 'completada', 1, '2026-05-11 23:33:50', '2026-05-11 22:41:57', '2026-05-11 23:33:50'),
(2, NULL, 7, 1, 30, 'Farmacéutica Nacional', NULL, NULL, NULL, 'Samur medicamento común y que salen.', 'completada', 1, '2026-05-11 23:34:14', '2026-05-11 22:41:57', '2026-05-11 23:34:14'),
(3, NULL, 3, 1, 45, 'Farmacéutica Nacional', NULL, NULL, NULL, 'Samur medicamento común y que salen.', 'completada', 1, '2026-05-11 23:33:55', '2026-05-11 22:41:57', '2026-05-11 23:33:55'),
(4, NULL, 2, 1, 25, 'MedSupply', NULL, NULL, NULL, NULL, 'completada', 1, '2026-05-11 23:08:12', '2026-05-11 23:08:00', '2026-05-11 23:08:12'),
(5, 'SOL-202605-0001', 5, 1, 40, 'Farmacéutica Nacional', NULL, NULL, NULL, NULL, 'completada', 1, '2026-05-11 23:33:32', '2026-05-11 23:32:31', '2026-05-11 23:33:32'),
(6, 'SOL-202605-0002', 3, 1, 50, 'Farmacéutica Nacional', NULL, NULL, NULL, NULL, 'completada', 1, '2026-05-13 03:47:00', '2026-05-13 03:37:12', '2026-05-13 03:47:00'),
(7, 'SOL-202605-0003', 2, 1, 25, 'MedSupply', NULL, NULL, NULL, NULL, 'completada', 4, '2026-05-13 04:32:33', '2026-05-13 04:31:25', '2026-05-13 04:32:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_items`
--

CREATE TABLE `solicitud_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `solicitud_id` bigint(20) UNSIGNED NOT NULL,
  `medicamento_id` bigint(20) UNSIGNED NOT NULL,
  `cantidad_solicitada` int(11) NOT NULL,
  `proveedor_sugerido` varchar(150) DEFAULT NULL,
  `telefono_proveedor` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `rol` enum('admin','medico','recepcionista') NOT NULL DEFAULT 'recepcionista',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` datetime DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `email`, `password`, `nombre_completo`, `rol`, `activo`, `ultimo_acceso`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@sisgeclin.com', '$2y$12$M7WC0Y75oTykJPIDZceNI.npL4Y05EvgB/xZNuXbdZOvkG2nshsLy', 'Administrador Sistema', 'admin', 1, '2026-05-12 23:12:09', 'eAKHeJpTSRzEOzS3h62z2Z6IEG9WgKcy6VC34eD6voOllzbAVx62x0VkQFjY', '2026-05-10 17:28:33', '2026-05-13 03:12:09'),
(2, 'medico', 'medico@sisgeclin.com', '$2y$12$M7WC0Y75oTykJPIDZceNI.npL4Y05EvgB/xZNuXbdZOvkG2nshsLy', 'Dr. Roberto Méndez', 'medico', 1, '2026-05-11 19:10:46', NULL, '2026-05-10 17:28:33', '2026-05-11 23:10:46'),
(3, 'recepcion', 'recepcion@sisgeclin.com', '$2y$12$M7WC0Y75oTykJPIDZceNI.npL4Y05EvgB/xZNuXbdZOvkG2nshsLy', 'María Torres', 'recepcionista', 1, NULL, NULL, '2026-05-10 17:28:33', '2026-05-10 20:54:00'),
(4, 'Blacke2f', 'enzoeliud1010@gmail.com', '$2y$12$M7WC0Y75oTykJPIDZceNI.npL4Y05EvgB/xZNuXbdZOvkG2nshsLy', 'Enzo Colmenarez', 'admin', 1, '2026-05-13 16:01:17', '7kQehKjGoFBVq3U4nZTTeFpwxZzJmcRKsnrlBv3cCayzhBIAB60RsmcyZswz', '2026-05-10 18:23:42', '2026-05-13 20:01:17'),
(7, 'coquerokli', 'enzoeliud@hotmail.com', '$2y$12$vkgp85k3fCbm9VlwjOuvau/c9qgbM25pjRtYeSp6.x6sVo2Udsw9e', 'jonas mendoza', 'recepcionista', 1, '2026-05-13 15:22:25', 'guXJyArujpio9HEA4R2OpPzdexOt8Rlymyvuu7vyYhoOoryy6g3pTTvvEClt', '2026-05-13 19:21:33', '2026-05-13 19:22:25');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bitacora_usuario_id_foreign` (`usuario_id`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultas_paciente_id_foreign` (`paciente_id`),
  ADD KEY `consultas_medico_id_foreign` (`medico_id`),
  ADD KEY `consultas_empleado_id_foreign` (`empleado_id`);

--
-- Indices de la tabla `consultas_medicamentos`
--
ALTER TABLE `consultas_medicamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultas_medicamentos_consulta_id_foreign` (`consulta_id`),
  ADD KEY `consultas_medicamentos_medicamento_id_foreign` (`medicamento_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventario_movimientos`
--
ALTER TABLE `inventario_movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventario_movimientos_medicamento_id_foreign` (`medicamento_id`),
  ADD KEY `inventario_movimientos_consulta_id_foreign` (`consulta_id`),
  ADD KEY `inventario_movimientos_usuario_id_foreign` (`usuario_id`),
  ADD KEY `inventario_movimientos_paciente_id_foreign` (`paciente_id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mensajes_remitente_id_foreign` (`remitente_id`),
  ADD KEY `mensajes_destinatario_id_foreign` (`destinatario_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `nomina`
--
ALTER TABLE `nomina`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomina_cedula_unique` (`cedula`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notificaciones_usuario_id_foreign` (`usuario_id`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pacientes_cedula_unique` (`cedula`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permisos_usuario`
--
ALTER TABLE `permisos_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permisos_usuario_usuario_id_modulo_unique` (`usuario_id`,`modulo`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `solicitudes_inventario`
--
ALTER TABLE `solicitudes_inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `solicitudes_inventario_numero_orden_unique` (`numero_orden`),
  ADD KEY `solicitudes_inventario_medicamento_id_foreign` (`medicamento_id`),
  ADD KEY `solicitudes_inventario_solicitado_por_foreign` (`solicitado_por`),
  ADD KEY `solicitudes_inventario_atendida_por_foreign` (`atendida_por`);

--
-- Indices de la tabla `solicitud_items`
--
ALTER TABLE `solicitud_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitud_items_solicitud_id_foreign` (`solicitud_id`),
  ADD KEY `solicitud_items_medicamento_id_foreign` (`medicamento_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuarios_username_unique` (`username`),
  ADD UNIQUE KEY `usuarios_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT de la tabla `consultas`
--
ALTER TABLE `consultas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `consultas_medicamentos`
--
ALTER TABLE `consultas_medicamentos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `inventario_movimientos`
--
ALTER TABLE `inventario_movimientos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `nomina`
--
ALTER TABLE `nomina`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `permisos_usuario`
--
ALTER TABLE `permisos_usuario`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `solicitudes_inventario`
--
ALTER TABLE `solicitudes_inventario`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `solicitud_items`
--
ALTER TABLE `solicitud_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD CONSTRAINT `consultas_empleado_id_foreign` FOREIGN KEY (`empleado_id`) REFERENCES `nomina` (`id`),
  ADD CONSTRAINT `consultas_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `consultas_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `consultas_medicamentos`
--
ALTER TABLE `consultas_medicamentos`
  ADD CONSTRAINT `consultas_medicamentos_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `consultas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultas_medicamentos_medicamento_id_foreign` FOREIGN KEY (`medicamento_id`) REFERENCES `inventario` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inventario_movimientos`
--
ALTER TABLE `inventario_movimientos`
  ADD CONSTRAINT `inventario_movimientos_consulta_id_foreign` FOREIGN KEY (`consulta_id`) REFERENCES `consultas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventario_movimientos_medicamento_id_foreign` FOREIGN KEY (`medicamento_id`) REFERENCES `inventario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventario_movimientos_paciente_id_foreign` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventario_movimientos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_destinatario_id_foreign` FOREIGN KEY (`destinatario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_remitente_id_foreign` FOREIGN KEY (`remitente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permisos_usuario`
--
ALTER TABLE `permisos_usuario`
  ADD CONSTRAINT `permisos_usuario_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes_inventario`
--
ALTER TABLE `solicitudes_inventario`
  ADD CONSTRAINT `solicitudes_inventario_atendida_por_foreign` FOREIGN KEY (`atendida_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `solicitudes_inventario_medicamento_id_foreign` FOREIGN KEY (`medicamento_id`) REFERENCES `inventario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitudes_inventario_solicitado_por_foreign` FOREIGN KEY (`solicitado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitud_items`
--
ALTER TABLE `solicitud_items`
  ADD CONSTRAINT `solicitud_items_medicamento_id_foreign` FOREIGN KEY (`medicamento_id`) REFERENCES `inventario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitud_items_solicitud_id_foreign` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes_inventario` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
