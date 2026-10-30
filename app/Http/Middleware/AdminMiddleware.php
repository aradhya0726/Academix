<?php

namespace App\Http\Middleware;

class AdminMiddleware extends RoleMiddleware
{
    protected function role(): string
    {
        return 'admin';
    }
}