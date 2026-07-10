<?php
use Illuminate\Support\Facades\DB;

$currentRoles = DB::table('roles')->pluck('name', 'id')->toArray();
// e.g. [1 => 'cowd', 2 => 'chm', 4 => 'superadmin', ...]

$desiredOrder = [
    1 => 'superadmin',
    2 => 'hardwareadmin',
    3 => 'chm',
    4 => 'programmer',
    5 => 'cowd',
    6 => 'admin'
];

DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('roles')->truncate();

$nameToNewId = [];
foreach ($desiredOrder as $id => $name) {
    DB::table('roles')->insert([
        'id' => $id,
        'name' => $name,
        'guard_name' => 'web',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    $nameToNewId[$name] = $id;
}

// Update users
$users = DB::table('users')->get();
foreach ($users as $user) {
    if ($user->role_id && isset($currentRoles[$user->role_id])) {
        $roleName = $currentRoles[$user->role_id];
        // The roleName might have a "-temp" if they changed it manually, but let's assume it doesn't.
        // Wait, looking at the screenshot, they didn't succeed in changing superadmin because of the error.
        if (isset($nameToNewId[$roleName])) {
            DB::table('users')->where('id', $user->id)->update(['role_id' => $nameToNewId[$roleName]]);
        }
    }
}
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Done fixing roles!\n";
