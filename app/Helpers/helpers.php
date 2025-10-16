<?php
/**
 * Funciones Helper Globales
 * Funciones útiles disponibles en toda la aplicación
 */

/**
 * Generar hash de contraseña
 * Útil para crear usuarios manualmente
 */
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Formatear fecha en español
 */
function formatDateES($date) {
    $months = [
        'January' => 'Enero', 'February' => 'Febrero', 'March' => 'Marzo',
        'April' => 'Abril', 'May' => 'Mayo', 'June' => 'Junio',
        'July' => 'Julio', 'August' => 'Agosto', 'September' => 'Septiembre',
        'October' => 'Octubre', 'November' => 'Noviembre', 'December' => 'Diciembre'
    ];
    
    $days = [
        'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];
    
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    $formatted = date('l, d \d\e F \d\e Y', $timestamp);
    
    return str_replace(array_keys($days + $months), array_values($days + $months), $formatted);
}

/**
 * Calcular días entre dos fechas
 */
function daysBetween($date1, $date2 = null) {
    $date2 = $date2 ?? date('Y-m-d');
    $diff = strtotime($date1) - strtotime($date2);
    return round($diff / (60 * 60 * 24));
}

/**
 * Validar fecha
 */
function isValidDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Truncar texto
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Obtener iniciales de un nombre
 */
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }
    return substr($initials, 0, 2);
}

/**
 * Generar color aleatorio hexadecimal
 */
function randomColor() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

/**
 * Validar ISBN
 */
function isValidISBN($isbn) {
    $isbn = str_replace(['-', ' '], '', $isbn);
    
    // ISBN-10
    if (strlen($isbn) == 10) {
        $check = 0;
        for ($i = 0; $i < 10; $i++) {
            $check += (10 - $i) * ($isbn[$i] == 'X' ? 10 : (int)$isbn[$i]);
        }
        return $check % 11 == 0;
    }
    
    // ISBN-13
    if (strlen($isbn) == 13) {
        $check = 0;
        for ($i = 0; $i < 13; $i++) {
            $check += (($i % 2 == 0) ? 1 : 3) * (int)$isbn[$i];
        }
        return $check % 10 == 0;
    }
    
    return false;
}

/**
 * Escapar HTML para prevenir XSS
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener valor de array con valor por defecto
 */
function array_get($array, $key, $default = null) {
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Debug helper
 */
function dd(...$vars) {
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}

/**
 * Convertir bytes a formato legible
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Generar token aleatorio
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Limpiar nombre de archivo
 */
function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    return preg_replace('/_+/', '_', $filename);
}

/**
 * Obtener extensión de archivo
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Verificar si es una imagen
 */
function isImage($filename) {
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    return in_array(getFileExtension($filename), $imageExtensions);
}

/**
 * Formatear número con separadores de miles
 */
function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals, ',', '.');
}

/**
 * Obtener edad desde fecha de nacimiento
 */
function getAge($birthdate) {
    $birth = new DateTime($birthdate);
    $today = new DateTime('today');
    return $birth->diff($today)->y;
}

/**
 * Generar slug desde texto
 */
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Verificar si es una solicitud AJAX
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Respuesta JSON
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Obtener IP del cliente
 */
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

/**
 * Logger simple
 */
function logMessage($message, $level = 'INFO') {
    $logFile = STORAGE_PATH . '/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}