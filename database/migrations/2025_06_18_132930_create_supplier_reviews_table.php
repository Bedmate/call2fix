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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('reviewer_id'); // buyer/user
            $table->unsignedBigInteger('supplier_id'); // product owner/seller

            $table->tinyInteger('product_accuracy');
            $table->tinyInteger('timeliness');
            $table->tinyInteger('condition_on_arrival');
            $table->tinyInteger('communication');
            $table->tinyInteger('professionalism');
            $table->tinyInteger('value_for_money');
            $table->text('comment')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_reviews');
    }
};
