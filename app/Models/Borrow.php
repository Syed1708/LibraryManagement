<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'late_fee',
        'fees',
        'borrow_date',
        'due_date',
        'return_date',
        'qty',
    ];
}
