<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('type')->default('municipality');
            $table->string('verification_status')->default('unverified');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipality_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('access_notes')->nullable();
            $table->text('source_url')->nullable();
            $table->string('source_label')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('verification_status')->default('unverified')->index();
            $table->timestampTz('verified_at')->nullable();
            $table->timestampTz('published_at')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['municipality_id', 'category_id', 'status']);
        });

        Schema::create('destination_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->text('url');
            $table->text('alt_text');
            $table->string('attribution')->nullable();
            $table->text('source_url')->nullable();
            $table->string('license')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('verification_status')->default('unverified');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('destination_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('verification_status')->default('unverified');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('destination_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('status')->default('draft')->index();
            $table->timestampTz('published_at')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_updates');
        Schema::dropIfExists('destination_highlights');
        Schema::dropIfExists('destination_photos');
        Schema::dropIfExists('destinations');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('municipalities');
    }
};
