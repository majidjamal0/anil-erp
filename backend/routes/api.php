<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    DB::select('select 1');

    if (! app()->environment('testing')) {
        Redis::connection()->ping();
    }

    return response()->json([
        'status' => 'ok',
        'service' => 'anil-erp-api',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health');
