<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name')->default('web');
                $table->timestamps();
                
                $table->unique(['name', 'guard_name']);
            });
        }

        // 1. Make sure all roles exist in the roles table
        $existingRoles = DB::table('roles')->pluck('id', 'name')->toArray();
        $userRoles = DB::table('users')->distinct()->pluck('role')->toArray();

        foreach ($userRoles as $roleName) {
            if ($roleName && !array_key_exists($roleName, $existingRoles)) {
                $id = DB::table('roles')->insertGetId([
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $existingRoles[$roleName] = $id;
            }
        }

        // 2. Convert string roles to numeric IDs in the users table
        foreach ($existingRoles as $roleName => $roleId) {
            DB::table('users')
                ->where('role', $roleName)
                ->update(['role' => $roleId]);
        }

        // 3. Rename the column and change its type
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role', 'role_id');
        });

        Schema::table('users', function (Blueprint $table) {
            // Need Doctrine DBAL for this to work, but since it's just integer casting
            // we will cast it to unsigned big integer
            $table->unsignedBigInteger('role_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role_id')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role_id', 'role');
        });

        // Best effort rollback: convert IDs back to strings
        $roles = DB::table('roles')->pluck('name', 'id')->toArray();
        foreach ($roles as $id => $name) {
            DB::table('users')
                ->where('role', $id)
                ->update(['role' => $name]);
        }
    }
};
