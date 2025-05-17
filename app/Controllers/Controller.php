<?php

namespace App\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class Controller
{
    /**
     * Show a view with data
     */
    protected function view($view, $data = [])
    {
        return View::make($view, $data);
    }

    /**
     * Redirect to a route
     */
    protected function redirect($route, $data = [])
    {
        return Redirect::route($route, $data);
    }

    /**
     * Get current user
     */
    protected function user()
    {
        return Session::get('user');
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated()
    {
        return Session::has('user_id');
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin()
    {
        $user = $this->user();
        return $user && $user['role'] === 'admin';
    }

    /**
     * Validate request data
     */
    protected function validate(Request $request, array $rules)
    {
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        return $request->all();
    }

    /**
     * Handle errors
     */
    protected function handleError($message, $code = 400)
    {
        Session::flash('error', $message);
        return Redirect::back()->withInput()->withStatus($code);
    }

    /**
     * Handle success
     */
    protected function handleSuccess($message, $route = null)
    {
        Session::flash('success', $message);
        if ($route) {
            return Redirect::route($route);
        }
        return Redirect::back();
    }
}
