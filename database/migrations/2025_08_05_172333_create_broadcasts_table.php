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
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('message');
            $table->json('recipients'); // JSON array of role names
            $table->foreignId('sender')->constrained('users')->onDelete('cascade');
            $table->timestamp('processed')->nullable();
            $table->timestamps(); // includes 'created_at' (your "created" field)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
