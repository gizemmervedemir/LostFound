<?php

// Database query function
function query($sql, $params = [])
{
    global $db;
    
    // Prepare statement
    $stmt = mysqli_prepare($db, $sql);
    
    if ($stmt === false) {
        throw new Exception("Query preparation failed: " . mysqli_error($db));
    }
    
    // Bind parameters if any
    if (!empty($params)) {
        $types = '';
        $bind_params = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $bind_params[] = $param;
        }
        
        array_unshift($bind_params, $types);
        call_user_func_array(["mysqli_stmt", "bind_param"], $bind_params);
    }
    
    // Execute query
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Query execution failed: " . mysqli_stmt_error($stmt));
    }
    
    // Get results
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
    
    return $rows;
}

// Asset URL helper
function asset($path)
{
    return '/public/' . ltrim($path, '/');
}

// Session helper
function session($key, $default = null)
{
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}
