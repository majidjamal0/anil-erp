<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('type')->default('retail_branch')->index();
            $table->foreignUuid('parent_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignUuid('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_operational')->default(true);
            $table->boolean('is_external')->default(false);
            $table->unsignedInteger('sort_order')->default(100);
            $table->json('metadata')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unique(['company_id','code']);
            $table->index(['company_id','is_active','type']);
        });
    }
    public function down(): void { Schema::dropIfExists('branches'); }
};
