<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'phone',
        'message',
        'gateway',
        'status',
        'response',
    ];
}
