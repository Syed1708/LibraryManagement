<?php

namespace App\Models;

use Carbon\Carbon;
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
            // dd($borrow->return_date);
            // if return date is not null then increment otherwise not 
            if($borrow->return_date == null){
                foreach ($borrow->items as $item) {
                    $book = Book::find($item->book_id);
                    
                    if ($book) {
                        $book->increment('available_copies', $item->qty);
                    }
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

    // public function calculateLateFee($returnDate)
    // {
    //     if ($returnDate > $this->due_date) {
    //         $daysLate = $returnDate->diffInDays($this->due_date);
    //         dd($daysLate);
    //         return $this->late_fee * $daysLate;
    //     }
    //     return 0;
    // }

    // public function returnBooks($returnDate)
    // {
    //     foreach ($this->items as $item) {
    //         $book = Book::find($item->book_id);
    //         $book->increment('available_copies', $item->qty);
    //     }

    //     $this->return_date = $returnDate;
    //     $this->late_fee = $this->calculateLateFee($returnDate);
    //     $this->payable = $this->fees + $this->late_fee;
    //     $this->save();
    // }



    public function calculateLateFee(Carbon $returnDate)
    {
        // Ensure due_date is a Carbon instance
        $dueDate = Carbon::parse($this->due_date);
        // $dueDate = $this->due_date;
    
        // Debugging: Print out the dates
        // dd([
        //     'due_date_raw' => $this->due_date,
        //     'due_date' => $dueDate->toDateString(),
        //     'return_date' => $returnDate->toDateString(),
        // ]);
    // Calculate the difference in days explicitly ensuring it is positive
    if ($returnDate->greaterThan($dueDate)) {
        $daysLate = floor($dueDate->diffInDays($returnDate));
        // dd($this->late_fee * $daysLate);
        return $this->late_fee * $daysLate;
    } 

    // Debugging: Print out the days late
    
 
     // Debugging: Print out the days late
    //  dd($daysLate);
    
        
    return 0;

    }
    
    public function returnBooks(Carbon $returnDate)
    {
        foreach ($this->items as $item) {
            $book = Book::find($item->book_id);
            $book->increment('available_copies', $item->qty);
        }
    
        $this->return_date = $returnDate;
        $lateFee = $this->calculateLateFee($returnDate);
        $this->late_fee = $lateFee;
        $this->payable = $this->fees + $lateFee;
        // dd($this->payable);
        $this->save();
    }
    

}
