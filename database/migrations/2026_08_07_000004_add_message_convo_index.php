<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Serves MessageController::thread() — the OR-wrapped
 *   WHERE (from_id=? AND to_id=?) OR (from_id=? AND to_id=?)
 * lookup (ad thread history). The existing idx_messages_inbox_unseen
 * (from_id,to_id,seen) and idx_messages_thread are narrower; this wider
 * (from_id,to_id,message_date) ordering covers the conversation sort.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('messages')) {
            return;
        }
        Schema::table('messages', function (Blueprint $t) {
            $t->index(['from_id', 'to_id', 'message_date'], 'idx_messages_convo');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('messages')) {
            return;
        }
        Schema::table('messages', function (Blueprint $t) {
            try {
                $t->dropIndex('idx_messages_convo');
            } catch (Throwable $e) { /* ignore */
            }
        });
    }
};
