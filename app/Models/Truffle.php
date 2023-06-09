<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Truffle extends Authenticatable
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'sku',
        'weight',
        'price',
        'created_at',
        'expires_at',
    ];
}
