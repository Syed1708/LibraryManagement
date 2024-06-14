<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowItem extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'book_id',
        'borrow_id',
        'late_fee',
        'fees',
        'qty',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            $book = Book::find($item->book_id);
            $book->decrement('available_copies', $item->qty);
        });

        static::deleted(function ($item) {
            $book = Book::find($item->book_id);
            $book->increment('available_copies', $item->qty);
        });
    }

    public function borrow()
    {
        return $this->belongsTo(Borrow::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
