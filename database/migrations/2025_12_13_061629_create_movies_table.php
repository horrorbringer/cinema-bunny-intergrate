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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cdn_path');              // file name/path in Bunny
            $table->string('thumbnail')->nullable(); // thumbnail image path
            $table->string('poster')->nullable();    // poster image path
            $table->integer('year')->nullable();
            $table->integer('duration')->nullable(); // duration in minutes
            $table->decimal('rating', 3, 1)->default(0); // rating out of 10
            $table->string('age_rating')->nullable(); // PG, PG-13, R, etc.
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->integer('views')->default(0);
            $table->timestamp('rental_expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
