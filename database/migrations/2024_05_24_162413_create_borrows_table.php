<?php

use App\Models\Book;
use App\Models\User;
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
        // Books borrows like products carts
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignId('student_id')->constrained('users');
            $table->string('borrow_no');
            $table->date('borrow_date'); //when borrow a book
            $table->date('due_date'); //deadline to return a book
            $table->date('return_date')->nullable(); //when user actually return a book
            $table->string('fees',50);
            $table->string('late_fee',50);
            $table->string('payable',50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
