<?php

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
        Schema::create('borrow_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrow_id')
            ->constrained('borrows')
            ->cascadeOnDelete();
            $table->foreignId('book_id')
            ->constrained('books')
            ->cascadeOnDelete();

            $table->string('fees');
            $table->string('late_fee');

            $table->unsignedBigInteger('qty')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_items');
    }
};
