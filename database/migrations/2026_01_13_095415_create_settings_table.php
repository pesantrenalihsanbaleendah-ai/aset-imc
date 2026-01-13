<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, image, number, boolean
            $table->string('group')->default('general'); // general, whatsapp, appearance, etc
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            // General Settings
            ['key' => 'site_name', 'value' => 'Sistem Manajemen Aset IMC', 'type' => 'text', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_description', 'value' => 'Sistem Informasi Manajemen Aset', 'type' => 'textarea', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_email', 'value' => 'admin@imc.com', 'type' => 'text', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_phone', 'value' => '021-12345678', 'type' => 'text', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],

            // Appearance Settings
            ['key' => 'site_logo', 'value' => null, 'type' => 'image', 'group' => 'appearance', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_favicon', 'value' => null, 'type' => 'image', 'group' => 'appearance', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'primary_color', 'value' => '#4e73df', 'type' => 'color', 'group' => 'appearance', 'created_at' => now(), 'updated_at' => now()],

            // WhatsApp Gateway Settings
            ['key' => 'whatsapp_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'whatsapp', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'whatsapp_endpoint', 'value' => '', 'type' => 'text', 'group' => 'whatsapp', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'whatsapp_api_key', 'value' => '', 'type' => 'text', 'group' => 'whatsapp', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'whatsapp_sender_number', 'value' => '', 'type' => 'text', 'group' => 'whatsapp', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'whatsapp_receiver_number', 'value' => '', 'type' => 'text', 'group' => 'whatsapp', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'whatsapp_group_id', 'value' => '', 'type' => 'text', 'group' => 'whatsapp', 'created_at' => now(), 'updated_at' => now()],

            // Notification Settings
            ['key' => 'notification_email', 'value' => '1', 'type' => 'boolean', 'group' => 'notification', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'notification_whatsapp', 'value' => '0', 'type' => 'boolean', 'group' => 'notification', 'created_at' => now(), 'updated_at' => now()],

            // Asset Settings
            ['key' => 'asset_code_prefix', 'value' => 'AST', 'type' => 'text', 'group' => 'asset', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'auto_generate_qr', 'value' => '0', 'type' => 'boolean', 'group' => 'asset', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'require_approval_loan', 'value' => '1', 'type' => 'boolean', 'group' => 'asset', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'require_approval_maintenance', 'value' => '1', 'type' => 'boolean', 'group' => 'asset', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
