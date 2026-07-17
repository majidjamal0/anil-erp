<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('branches', function(Blueprint $t) { $t->uuid('id')->primary(); $t->string('code')->unique(); $t->string('name'); $t->text('address')->nullable(); $t->string('phone')->nullable(); $t->boolean('is_active')->default(true); $t->timestampsTz(); $t->softDeletesTz(); }); } public function down(): void { Schema::dropIfExists('branches'); } };
