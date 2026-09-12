<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Rename auto-generated `guest{phone}` usernames to name slugs so public
 * profile URLs are shareable (/store/hi-hello instead of
 * /store/guest01333333333).
 *
 * Only untouched auto rows are renamed (username still matches ^guest\d+$);
 * message sender snapshots follow the rename. Skips rows whose name has
 * nothing URL-safe or whose slug is already taken.
 */
return new class extends Migration
{
    public function up(): void
    {
        try {
            $users = DB::table('user')
                ->where('username', 'like', 'guest%')
                ->select('id', 'username', 'name')
                ->get();
        } catch (Throwable) {
            return;
        }

        foreach ($users as $user) {
            if (!preg_match('/^guest\d+$/', (string) $user->username)) {
                continue;
            }

            $base = (string) Str::slug(trim((string) ($user->name ?? '')), '-');
            $base = (string) preg_replace('/[^A-Za-z0-9_.-]+/', '', $base);
            $base = trim($base, '._-');
            if (strlen($base) < 3) {
                continue;
            }
            $base = substr($base, 0, 37);

            $candidate = $base;
            $i = 1;
            while (DB::table('user')->where('username', $candidate)->exists()) {
                $i++;
                if ($i > 50) {
                    continue 2;
                }
                $suffix = '-'.$i;
                $candidate = substr($base, 0, 40 - strlen($suffix)).$suffix;
            }

            DB::table('user')->where('id', $user->id)->update([
                'username' => $candidate,
                'updated_at' => now(),
            ]);

            try {
                DB::table('messages')->where('from_uname', $user->username)->update(['from_uname' => $candidate]);
                DB::table('messages')->where('to_uname', $user->username)->update(['to_uname' => $candidate]);
            } catch (Throwable) {
                // Message snapshots are display-only; the rename stands.
            }
        }
    }

    public function down(): void
    {
        // One-way rename — previous auto values are not retained.
    }
};
