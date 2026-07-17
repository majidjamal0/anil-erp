<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('warehouses', function(Blueprint $t) { $t->uuid('id')->primary(); $t->foreignUuid('branch_id')->constrained()->restrictOnDelete(); $t->string('code')->unique(); $t->string('name'); $t->boolean('is_active')->default(true); $t->timestampsTz(); $t->softDeletesTz(); }); } public function down(): void { Schema::dropIfExists('warehouses'); } };
