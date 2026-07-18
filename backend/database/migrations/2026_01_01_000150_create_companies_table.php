<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('code')->unique();
            $table->string('national_id')->nullable();
            $table->string('economic_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('default_locale')->default('fa');
            $table->string('default_currency')->default('IRR');
            $table->string('timezone')->default('Asia/Tehran');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
