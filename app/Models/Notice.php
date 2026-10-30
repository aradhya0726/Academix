<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notice extends Model
{
    protected $fillable = [
        'user_id', 'title', 'message', 'audience', 'class_id',
        'link', 'image', 'attachment', 'attachment_name',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classroom()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function scopeVisibleTo($query, $user)
    {
        if (!$user || $user->role === 'admin') {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('audience', 'all')
              ->orWhere('user_id', $user->id);

            if ($user->role === 'teacher') {
                $classIds = DB::table('subject')
                    ->where('teacher_id', $user->id)
                    ->pluck('class_id');

                $q->orWhere('audience', 'teachers')
                  ->orWhere(function ($q2) use ($classIds) {
                      $q2->where('audience', 'class')->whereIn('class_id', $classIds);
                  });
            } else {
                $q->orWhere('audience', 'students')
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('audience', 'class')->where('class_id', $user->class_id);
                  });
            }
        });
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }
}