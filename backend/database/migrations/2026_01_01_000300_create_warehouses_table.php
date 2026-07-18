<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('branch_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignUuid('warehouse_type_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sellable')->default(false);
            $table->boolean('is_shippable')->default(false);
            $table->unsignedInteger('allocation_priority')->default(100);
            $table->json('metadata')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unique(['company_id','code']);
            $table->index(['company_id','branch_id','is_active']);
        });
    }
    public function down(): void { Schema::dropIfExists('warehouses'); }
};
