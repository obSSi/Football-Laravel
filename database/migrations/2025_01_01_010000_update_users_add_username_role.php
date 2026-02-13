<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->unique()->after('name');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'visiteur'])->default('visiteur')->after('email');
            }
        });

        if (Schema::hasColumn('users', 'username')) {
            $users = DB::table('users')->get(['id', 'username', 'name', 'email']);

            foreach ($users as $user) {
                if ($user->username) {
                    continue;
                }

                $base = $user->name ?: ($user->email ? Str::before($user->email, '@') : 'user'.$user->id);
                $slug = Str::slug($base, '_');
                if ($slug === '') {
                    $slug = 'user_'.$user->id;
                }

                $candidate = $slug;
                $suffix = 1;
                while (DB::table('users')->where('username', $candidate)->exists()) {
                    $candidate = $slug.'_'.$suffix;
                    $suffix++;
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => $candidate]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique('users_username_unique');
                $table->dropColumn('username');
            }
        });
    }
};
