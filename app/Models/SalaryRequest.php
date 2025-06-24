<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRequest extends Model
{
    protected $fillable = [
        'user_id',
        'base_salary',
        'bonus',
        'pph_percentage',
        'pph_amount',
        'net_salary',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'processed_by',
        'processed_at',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'id',
        'user_id',
        'approved_by',
        'processed_by'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
