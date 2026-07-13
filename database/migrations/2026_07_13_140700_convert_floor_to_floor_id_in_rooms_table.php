<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure floors exist
        $distinctFloors = DB::table('rooms')->whereNotNull('floor')->distinct()->pluck('floor')->toArray();
        $existingFloors = DB::table('floors')->pluck('id', 'name')->toArray();
        
        $maxSort = DB::table('floors')->max('sort_order') ?? 0;
        
        foreach ($distinctFloors as $floorName) {
            if (!array_key_exists($floorName, $existingFloors)) {
                $maxSort++;
                $id = DB::table('floors')->insertGetId([
                    'name' => $floorName,
                    'sort_order' => $maxSort,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $existingFloors[$floorName] = $id;
            }
        }
        
        // 2. Add floor_id column
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('floor_id')->nullable()->constrained('floors')->nullOnDelete();
        });
        
        // 3. Migrate data
        foreach ($existingFloors as $floorName => $floorId) {
            DB::table('rooms')->where('floor', $floorName)->update(['floor_id' => $floorId]);
        }
        
        // 4. Drop floor string column
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('floor');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('floor')->nullable();
        });
        
        $floors = DB::table('floors')->pluck('name', 'id')->toArray();
        
        foreach ($floors as $id => $name) {
            DB::table('rooms')->where('floor_id', $id)->update(['floor' => $name]);
        }
        
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['floor_id']);
            $table->dropColumn('floor_id');
        });
    }
};
