<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('max_timelines')->nullable()->after('max_users');
            $table->unsignedInteger('storage_mb')->nullable()->after('max_timelines');
            $table->boolean('can_use_integrations')->default(false)->after('storage_mb');
            $table->boolean('can_collaborate')->default(false)->after('can_use_integrations');
            $table->boolean('can_use_auto_sync')->default(false)->after('can_collaborate');
            $table->boolean('can_use_smart_automation')->default(false)->after('can_use_auto_sync');
            $table->boolean('can_use_activity_logs')->default(false)->after('can_use_smart_automation');
            $table->boolean('can_use_priority_sync')->default(false)->after('can_use_activity_logs');
            $table->boolean('can_use_advanced_privacy')->default(false)->after('can_use_priority_sync');
            $table->boolean('can_share_private_links')->default(false)->after('can_use_advanced_privacy');
            $table->boolean('can_use_insights')->default(false)->after('can_share_private_links');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'max_timelines',
                'storage_mb',
                'can_use_integrations',
                'can_collaborate',
                'can_use_auto_sync',
                'can_use_smart_automation',
                'can_use_activity_logs',
                'can_use_priority_sync',
                'can_use_advanced_privacy',
                'can_share_private_links',
                'can_use_insights',
            ]);
        });
    }
};
