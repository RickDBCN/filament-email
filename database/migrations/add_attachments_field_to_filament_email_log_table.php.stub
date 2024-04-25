<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('filament_email_log', function (Blueprint $table) {
            $table->after('sent_debug_info', function (Blueprint $table) {
                $table->json('attachments')->nullable()->default(null);
            });
        });
    }

    public function down()
    {
        Schema::table('filament_email_log', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
