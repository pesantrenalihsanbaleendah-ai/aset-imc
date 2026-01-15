<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('settings')->where('key', 'whatsapp_group_id')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->insert([
            'key' => 'whatsapp_group_id',
            'value' => '',
            'type' => 'text',
            'group' => 'whatsapp',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
};
