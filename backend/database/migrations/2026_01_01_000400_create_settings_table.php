<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('settings', function(Blueprint $t) { $t->uuid('id')->primary(); $t->string('group')->default('general'); $t->string('key'); $t->jsonb('value')->nullable(); $t->boolean('is_public')->default(false); $t->timestampsTz(); $t->unique(['group','key']); }); } public function down(): void { Schema::dropIfExists('settings'); } };
