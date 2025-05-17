<?php

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'lost_found');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create database connection
define('DB_CONNECTION', mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME));

// Check connection
if (mysqli_connect_error()) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Set charset to utf8mb4
mysqli_set_charset(DB_CONNECTION, 'utf8mb4');

// Application configuration
define('APP_NAME', $_ENV['APP_NAME']);
define('APP_URL', $_ENV['APP_URL']);
define('APP_DEBUG', $_ENV['APP_DEBUG'] === 'true');

// Mail configuration
define('MAIL_HOST', $_ENV['MAIL_HOST']);
define('MAIL_PORT', $_ENV['MAIL_PORT']);
define('MAIL_USERNAME', $_ENV['MAIL_USERNAME']);
define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD']);
define('MAIL_ENCRYPTION', $_ENV['MAIL_ENCRYPTION']);
define('MAIL_FROM_NAME', $_ENV['MAIL_FROM_NAME']);
define('MAIL_FROM_ADDRESS', $_ENV['MAIL_FROM_ADDRESS']);

// Image upload configuration
define('UPLOAD_PATH', __DIR__ . '/../../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Database helper functions
function query($sql, $params = []) {
    global $DB_CONNECTION;
    
    $stmt = mysqli_prepare($DB_CONNECTION, $sql);
    if (!$stmt) {
        throw new Exception('Query preparation failed: ' . mysqli_error($DB_CONNECTION));
    }
    
    if (!empty($params)) {
        $types = '';
        $bindParams = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
        }
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $data;
}

function insert($table, $data) {
    $fields = array_keys($data);
    $values = array_fill(0, count($fields), '?');
    $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
    $stmt = mysqli_prepare(DB_CONNECTION, $sql);
    $types = '';
    foreach ($data as $value) {
        $types .= gettype($value)[0];
    }
    mysqli_stmt_bind_param($stmt, $types, ...array_values($data));
    return mysqli_stmt_execute($stmt);
}

function update($table, $data, $where, $params) {
    $fields = array_keys($data);
    $set = implode(' = ?, ', $fields) . ' = ?';
    $sql = "UPDATE $table SET $set WHERE $where";
    $stmt = mysqli_prepare(DB_CONNECTION, $sql);
    $types = '';
    foreach ($data as $value) {
        $types .= gettype($value)[0];
    }
    foreach ($params as $value) {
        $types .= gettype($value)[0];
    }
    mysqli_stmt_bind_param($stmt, $types, ...array_values($data), ...$params);
    return mysqli_stmt_execute($stmt);
}

function delete($table, $where, $params) {
    $sql = "DELETE FROM $table WHERE $where";
    $stmt = mysqli_prepare(DB_CONNECTION, $sql);
    $types = '';
    foreach ($params as $value) {
        $types .= gettype($value)[0];
    }
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    return mysqli_stmt_execute($stmt);
}

function select($table, $where = null, $params = [], $columns = '*', $order = null, $limit = null) {
    $sql = "SELECT $columns FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    if ($order) {
        $sql .= " ORDER BY $order";
    }
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    return query($sql, $params);
}
