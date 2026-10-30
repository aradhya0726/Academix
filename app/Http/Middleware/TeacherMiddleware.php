<?php

namespace App\Http\Middleware;

class TeacherMiddleware extends RoleMiddleware
{
    protected function role(): string
    {
        return 'teacher';
    }
}