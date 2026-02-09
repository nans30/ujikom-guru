<?php

namespace App\Models;

use App\Helpers\Helpers;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\Teacher;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, SoftDeletes, Notifiable, InteractsWithMedia, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'phone',
        'dob',
        'gender',
        'status',
        'first_name',
        'last_name',
      
       
       
        'location',
   
   
    
     
        'created_by_id',
        'system_reserve',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone' => 'integer',
            'status' => 'integer',
            'created_by_id' => 'integer'
        ];
    }

    protected $appends = [
        'name',
        'role',
    ];

    protected $with = [
        'media'
    ];

    public static function booted()
    {
        parent::boot();
        static::saving(function ($model) {
            $model->created_by_id = Helpers::isUserLogin() ? \App\Helpers\Helpers::getCurrentUserId() : $model->id;
        });
    }

    /**
     * Get the user's role.
     */
    public function getRoleAttribute()
    {
        return $this->roles->first()?->makeHidden(['created_at', 'updated_at', 'pivot']);
    }

    /**
     * Get the user's all permissions.
     */
    public function getPermissionAttribute()
    {
        return $this->getAllPermissions();
    }

    public function country()
    {
      
    }

    public function getAvatarAttribute(): string
    {
        $image = $this->getFirstMedia('image');

        return $image ? $image->getUrl() : asset('admin/assets/images/user-images/tom.jpeg');
    }

    public function getNameAttribute(): string
    {
        if ($this->first_name || $this->last_name) {
            return Str::title(trim($this->first_name . ' ' . $this->last_name));
        }

        return $this->teacher?->name ?? $this->email;
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }
}