<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('group')->default('general');
            $table->string('key');
            $table->json('value')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestampsTz();
            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
