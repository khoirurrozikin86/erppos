<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'logo',
        'email',
        'phone',
        'website',
        'tax_number',
        'address',
        'city',
        'province',
        'postal_code',
        'currency',
        'timezone',
        'date_format',
        'invoice_header',
        'invoice_footer',
        'receipt_header',
        'receipt_footer',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
