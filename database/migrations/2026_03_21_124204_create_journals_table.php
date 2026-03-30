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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();

            // 5 Fields for the Rubric (excluding id, timestamps, softDeletes)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 1
            $table->string('title');                                        // 2
            $table->text('content');                                        // 3
            $table->string('mood')->nullable();                             // 4
            $table->boolean('is_favorite')->default(false);                 // 5

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
