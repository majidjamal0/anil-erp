<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['company_user'=>'company_id','branch_user'=>'branch_id','user_warehouse'=>'warehouse_id'] as $table => $foreign) {
            Schema::create($table, function (Blueprint $t) use ($table, $foreign): void {
                $t->uuid('id')->primary();
                $t->foreignUuid('user_id')->constrained()->cascadeOnDelete();
                $target = $foreign === 'company_id' ? 'companies' : ($foreign === 'branch_id' ? 'branches' : 'warehouses');
                $t->foreignUuid($foreign)->constrained($target)->cascadeOnDelete();
                $t->string('access_level')->default('view');
                $t->boolean('is_default')->default(false);
                $t->timestampsTz();
                $t->unique(['user_id', $foreign]);
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('user_warehouse'); Schema::dropIfExists('branch_user'); Schema::dropIfExists('company_user');
    }
};
