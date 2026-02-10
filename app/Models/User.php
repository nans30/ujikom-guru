<?php

namespace App\Models;

use App\Helpers\Helpers;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, SoftDeletes, Notifiable, InteractsWithMedia, HasRoles;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'email',
        'password',
        'gender',
        'dob',
        'status',
        'created_by_id',
        'system_reserve',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
            'created_by_id' => 'integer',
            'system_reserve' => 'integer',
            'dob' => 'date',
        ];
    }

    /**
     * Auto append attributes
     */
    protected $appends = [
        'name',
        'role',
        'avatar',
    ];

    /**
     * Always load media
     */
    protected $with = [
        'media',
    ];

    /**
     * Booted event
     */
    protected static function booted()
    {
        parent::boot();

        static::saving(function ($model) {
            if (Helpers::isUserLogin()) {
                $model->created_by_id = Helpers::getCurrentUserId();
            }
        });
    }

    /**
     * =========================
     * ACCESSORS
     * =========================
     */

    /** 👉 INI KUNCI OPSI 2 */
    public function getNameAttribute(): string
    {
        // kalau user punya teacher → ambil nama teacher
        if ($this->relationLoaded('teacher') || $this->teacher) {
            if ($this->teacher?->name) {
                return $this->teacher->name;
            }
        }

        // fallback terakhir
        return $this->email;
    }

    public function getRoleAttribute()
    {
        return $this->roles
            ->first()
            ?->makeHidden(['created_at', 'updated_at', 'pivot']);
    }

    public function getPermissionAttribute()
    {
        return $this->getAllPermissions();
    }

    public function getAvatarAttribute(): string
    {
        $image = $this->getFirstMedia('image');

        return $image
            ? $image->getUrl()
            : asset('admin/assets/images/user-images/tom.jpeg');
    }

    /**
     * Relation: User -> Teacher
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }
}