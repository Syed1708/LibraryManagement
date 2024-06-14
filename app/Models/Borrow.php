<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\This;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Borrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'borrow_no',
        'borrow_date',
        'due_date',
        'return_date',
        'fees',
        'late_fee',
        'payable',
    ];

    
    public function items()
    {
        return $this->hasMany(BorrowItem::class);
        // return $this->hasMany(BorrowItem::class, 'borrow_id');
    }
    // then create a Retrun Borrow Books where i will add late_fee conditionally
    // to store current user_id
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($borrow) {
            foreach ($borrow->items as $item) {
                $book = Book::find($item->book_id);
                if ($book) {
                    $book->increment('available_copies', $item->qty);
                }
            }
        });

        static::deleted(function ($borrow) {
            $borrow->items()->delete();
        });
    }



    // public function calculateTotalFees()
    // {
    //     $totalFees = 0;
    //     $totalLateFees = 0;

    //     foreach ($this->items as $item) {
    //         $totalFees += $item->fees * $item->qty;
    //         $totalLateFees += $item->late_fee;
    //     }

    //     $this->fees = $totalFees;
    //     $this->late_fee = $totalLateFees;
    //     $this->payable = $totalFees + $totalLateFees;
    // }

    
    
    public function librarian()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function calculateLateFee($returnDate)
    {
        if ($returnDate > $this->due_date) {
            $daysLate = $returnDate->diffInDays($this->due_date);
            return $this->late_fee * $daysLate;
        }
        return 0;
    }

    public function returnBooks($returnDate)
    {
        foreach ($this->items as $item) {
            $book = Book::find($item->book_id);
            $book->increment('available_copies', $item->qty);
        }

        $this->return_date = $returnDate;
        $this->late_fee = $this->calculateLateFee($returnDate);
        $this->payable = $this->fees + $this->late_fee;
        $this->save();
    }


}
