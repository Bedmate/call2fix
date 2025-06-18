<?php
namespace App\Observers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserObserver
{
    /**
     * Handle the User "deleting" event.
     *
     * This fires *before* the `deleted_at` timestamp is written to the users table
     * (so we know it’s a soft‑delete rather than a force‑delete).
     */
    public function deleting(User $user): void
    {
        // Skip if the developer is force‑deleting:
        if ($user->isForceDeleting()) {
            return;
        }

        $now = Carbon::now();

        // Grab every table name in the current connection:
        $tables = collect(DB::select('SHOW TABLES'))
            ->map(fn($row) => array_values((array) $row)[0])
            ->reject(fn($tbl) => $tbl === $user->getTable()); 

        foreach ($tables as $table) {
            // Only update tables that *both* have user_id & deleted_at columns
            if (
                Schema::hasColumn($table, 'user_id') &&
                Schema::hasColumn($table, 'deleted_at')
            ) {
                DB::table($table)
                    ->where('user_id', $user->getKey())
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => $now]);
            }
        }
    }
}
