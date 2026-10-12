<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'type',
        'template_en',
        'template_bn',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
