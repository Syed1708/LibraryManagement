<?php

use App\Models\Author;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->uuid('isbn');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignIdFor(Category::class);
            $table->foreignIdFor(Genre::class);
            $table->string('image');
            $table->date('published_year');
            $table->string('fees');
            $table->integer('copies');
            $table->integer('available_copies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
