<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        // Get recent items
        $items = query("
            SELECT * FROM items 
            WHERE status = 'active'
            ORDER BY created_at DESC
            LIMIT 6
        ");

        // Render view
        require __DIR__ . '/../Views/home.php';
    }
}
