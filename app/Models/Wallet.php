<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
    ];

    protected $casts = [
        'balance' => 'float'
    ];

    public $incrementing = false;

    public function users()
    {
        return $this->belongsTo(users::class);
    }
}
