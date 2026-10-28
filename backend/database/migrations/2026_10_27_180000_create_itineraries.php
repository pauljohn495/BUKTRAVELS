<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->date('start_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('itinerary_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->date('visit_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['itinerary_id', 'destination_id']);
            $table->index(['itinerary_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_stops');
        Schema::dropIfExists('itineraries');
    }
};
