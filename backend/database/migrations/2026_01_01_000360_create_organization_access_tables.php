<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'company_user' => 'company_id',
            'branch_user' => 'branch_id',
            'user_warehouse' => 'warehouse_id',
        ];

        foreach ($tables as $table => $foreign) {
            Schema::create($table, function (Blueprint $t) use ($foreign): void {
                $t->uuid('id')->primary();
                $t->foreignUuid('user_id')->constrained()->cascadeOnDelete();
                $target = match ($foreign) {
                    'company_id' => 'companies',
                    'branch_id' => 'branches',
                    default => 'warehouses',
                };
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
        Schema::dropIfExists('user_warehouse');
        Schema::dropIfExists('branch_user');
        Schema::dropIfExists('company_user');
    }
};
