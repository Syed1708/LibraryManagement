<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'title',
        'slug',
        'description',
        'text_color',
        'bg_color',
        'hover_color',
    ];

    public function books(){
        return $this->hasMany(Book::class);
    }

    
}
