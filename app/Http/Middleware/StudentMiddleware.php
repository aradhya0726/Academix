<?php

namespace App\Http\Middleware;

class StudentMiddleware extends RoleMiddleware
{
    protected function role(): string
    {
        return 'student';
    }
}