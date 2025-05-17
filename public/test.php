<?php

// Simple test page
header('Content-Type: text/html; charset=utf-8');

// Test PHP
echo "<h1>PHP Test Page</h1>";

// Test database connection
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lost_found');

try {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn) {
        echo "<h2>Database Connection Successful!</h2>";
    } else {
        echo "<h2>Database Connection Failed!</h2>";
    }
} catch (Exception $e) {
    echo "<h2>Database Connection Error:</h2>" . $e->getMessage();
}

// Test session
session_start();
$_SESSION['test'] = 'session_test';
if (isset($_SESSION['test'])) {
    echo "<h2>Session Working!</h2>";
}

// Test file permissions
$test_file = __DIR__ . '/test.txt';
if (file_put_contents($test_file, 'Test content')) {
    echo "<h2>File Writing Working!</h2>";
    unlink($test_file);
} else {
    echo "<h2>File Writing Error!</h2>";
}
