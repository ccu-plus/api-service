<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportUsersData extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $path = storage_path('app/users.json');

        if (!is_file($path)) {
            return;
        }

        $users = json_decode(file_get_contents($path), true);

        $migration = 1;

        foreach ($users as $user) {
            if ($user['nickname'] === 'c229f39e') {
                $username = sprintf('migration-%04d', $migration++);
                $nickname = $username;
                $email = $user['username'];
            } else {
                $username = $user['username'];
                $nickname = $user['nickname'];
                $email = null;
            }

            DB::table('users')->insert([
                'id' => $user['id'],
                'username' => $username,
                'nickname' => $nickname,
                'email' => $email,
                'token' => Str::random(16),
                'verified_at' => null,
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at'],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->truncate();
    }
}
