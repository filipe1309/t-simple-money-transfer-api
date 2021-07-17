<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
