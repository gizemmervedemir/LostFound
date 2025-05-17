<?php

namespace App\Models;

class User
{
    private $table = 'users';

    // Register new user
    public function register($name, $email, $password, $role = 'user')
    {
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role
        ];
        return insert($this->table, $data);
    }

    // Login user
    public function login($email, $password)
    {
        $users = select($this->table, "email = ?", [$email]);
        if (empty($users)) {
            return false;
        }
        
        $user = $users[0];
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // Get user by ID
    public function getById($id)
    {
        $users = select($this->table, "id = ?", [$id]);
        return empty($users) ? null : $users[0];
    }

    // Update user profile
    public function updateProfile($id, $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return update($this->table, $data, "id = ?", [$id]);
    }

    // Get user items
    public function getItems($userId)
    {
        return select('items', "user_id = ?", [$userId], '*', 'created_at DESC');
    }
}
