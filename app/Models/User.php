<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'phone_number',
    'photo',
    'class_id',
])]

#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** Role => dashboard route name. The only place this mapping lives. */
    public const ROLE_DASHBOARDS = [
        'admin'   => 'admin.dashboard',
        'teacher' => 'teacher.dashboard',
        'student' => 'student.dashboard',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /** Route name of this user's dashboard, or null for an unknown role. */
    public function dashboardRoute(): ?string
    {
        return self::ROLE_DASHBOARDS[$this->role] ?? null;
    }

    /** Public URL of the saved profile photo, or null if none / file missing. */
    public function photoUrl(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        $path = 'uploads/profile/' . basename($this->photo);

        return file_exists(public_path($path)) ? asset($path) : null;
    }

    /** Fallback avatar text, e.g. "AD". */
    public function initials(): string
    {
        $text = mb_strtoupper(mb_substr(trim((string) $this->name), 0, 2));

        return $text !== '' ? $text : 'U';
    }

    /** The class (faculty + semester) a student belongs to */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public static function getEmailSingle($email)
    {
        return self::where('email', $email)->first();
    }

    public static function getAdmin()
    {
        return self::where('role', 'admin')->orderBy('id', 'desc');
    }
}