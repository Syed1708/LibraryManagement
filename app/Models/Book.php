<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'isbn',
        'category_id',
        'genre_id',
        'image',
        'published_year',
        'fees',
        'copies',
        'available_copies',
    ];
 
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->isbn)) {
                $model->isbn = (string) Str::uuid();
            }
        });
        
    }
    public function authors()
    {

        return $this->belongsToMany(Author::class,'author_book')->withTimestamps();
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function genre(){
        return $this->belongsTo(Genre::class);
    }

    // public function borrows()
    // {
    //     return $this->hasMany(Borrow::class);
    // }
    // public function borrow()
    // {
    //     return $this->belongsTo(Borrow::class);
    // }

    public function borrowItems()
    {
        return $this->hasMany(BorrowItem::class);
    }

}
