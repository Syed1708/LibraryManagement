<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'author',
        'genre',
        'published_year',
        'copices',
        'avilable_copices',
    ];

    public function categories()
    {

        return $this->belongsToMany(Category::class);
    }
}
