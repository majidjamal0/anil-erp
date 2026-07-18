<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_channels', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('type')->index();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_warehouse_selection')->default(false);
            $table->foreignUuid('default_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->json('settings')->nullable();
            $table->unsignedInteger('sort_order')->default(100);
            $table->timestampsTz();
            $table->softDeletesTz();
            $table->unique(['company_id','code']);
        });
    }
    public function down(): void { Schema::dropIfExists('sales_channels'); }
};
