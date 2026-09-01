<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('status', 30)->default('active')->index();
            $table->string('avatar_path')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->unsignedSmallInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable()->index();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
        });

        if (! Schema::hasColumn('users', 'is_admin')) {
            return;
        }

        $roleId = DB::table('roles')->where('name', 'super_admin')->value('id');

        if (! is_int($roleId)) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Conta excepcional com acesso integral ao sistema.',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $now = now();
        DB::table('users')
            ->where('is_admin', true)
            ->orderBy('id')
            ->each(function (object $user) use ($roleId, $now): void {
                DB::table('user_roles')->insertOrIgnore([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'assigned_by' => null,
                    'expires_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_admin')->default(false);
        });

        DB::table('users')
            ->whereIn('id', DB::table('user_roles')
                ->select('user_id')
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('roles.name', 'super_admin'))
            ->update(['is_admin' => true]);

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'status',
                'avatar_path',
                'last_login_at',
                'password_changed_at',
                'failed_login_attempts',
                'locked_until',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_confirmed_at',
            ]);
        });
    }
};
