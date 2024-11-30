<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'masked_card_number',
        'amount',
        'currency',
        'customer_email',
        'metadata',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'masked_card_number' => 'string',
        'metadata' => 'array', 
    ];

    protected $hidden = [
        'card_number',
    ];

    public $incrementing = false;
    protected $keyType = 'string'; 

    public function getCardNumberAttribute()
    {
        return $this->masked_card_number;
    }
}


