<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereNull('is_admin')
            ->update(['is_admin' => false]);
    }

    public function down(): void
    {
        // Existing NULL values cannot be restored safely.
    }
};
