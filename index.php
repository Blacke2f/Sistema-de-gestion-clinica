<?php
/**
 * SISGECLIN - Punto de entrada raíz
 * Redirige automáticamente a public/
 * Funciona desde localhost, IP de red, o cualquier host
 */
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script   = dirname($_SERVER['SCRIPT_NAME']);
$base     = rtrim($script, '/');
$url      = "{$scheme}://{$host}{$base}/public/";

header("Location: {$url}", true, 301);
exit;
