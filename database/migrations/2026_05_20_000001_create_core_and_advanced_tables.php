<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create products table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category');
            $table->decimal('price', 15, 2);
            $table->integer('stock')->default(0);
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        // 2. Create posts table (CMS Blog)
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 3. Create tickets table (CRM Support Tickets)
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('Open'); // Open, In Progress, Closed
            $table->string('priority')->default('Medium'); // Low, Medium, High
            $table->timestamps();
        });

        // 4. Create reviews table (Reviews & Ratings)
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('rating'); // 1 to 5
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('products');
    }
};
