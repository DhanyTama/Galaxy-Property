<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($query) {
            $query->created_at = now();
            $query->updated_at = now();
        });

        static::updating(function ($query) {
            $query->updated_at = now();
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
