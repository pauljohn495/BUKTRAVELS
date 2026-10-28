<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destination_photos', function (Blueprint $table) {
            $table->string('disk')->nullable()->after('url');
            $table->text('path')->nullable()->after('disk');
            $table->string('original_name')->nullable()->after('path');
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'destination_id']);
        });

        Schema::create('audit_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action')->index();
            $table->string('auditable_type')->index();
            $table->unsignedBigInteger('auditable_id')->nullable()->index();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');
        Schema::dropIfExists('favorites');
        Schema::table('destination_photos', function (Blueprint $table) {
            $table->dropColumn(['disk', 'path', 'original_name']);
        });
    }
};
