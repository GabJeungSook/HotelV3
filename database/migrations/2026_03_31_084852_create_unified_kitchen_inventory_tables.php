<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create departments table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 2. Seed departments
        DB::table('departments')->insert([
            ['id' => 1, 'name' => 'Main Kitchen', 'slug' => 'kitchen', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Pub Kitchen', 'slug' => 'pub', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Frontdesk', 'slug' => 'frontdesk', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Create item_categories table (2-level: Food/Drinks → subcategories)
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('item_categories')->nullOnDelete();
        });

        // 4. Create menu_items table
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->foreignId('department_id');
            $table->foreignId('category_id')->nullable();
            $table->string('item_code')->nullable();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('item_categories')->nullOnDelete();
        });

        // 5. Create item_inventories table
        Schema::create('item_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id');
            $table->foreignId('menu_item_id')->unique();
            $table->double('number_of_serving')->default(0);
            $table->timestamps();

            $table->foreign('menu_item_id')->references('id')->on('menu_items')->cascadeOnDelete();
        });

        // 6. Migrate data from old tables
        $this->migrateData();

        // 7. Rename old tables (keep for safety)
        Schema::rename('menu_categories', 'menu_categories_old');
        Schema::rename('menus', 'menus_old');
        Schema::rename('inventories', 'inventories_old');
        Schema::rename('pub_categories', 'pub_categories_old');
        Schema::rename('pub_menus', 'pub_menus_old');
        Schema::rename('pub_inventories', 'pub_inventories_old');
        Schema::rename('frontdesk_categories', 'frontdesk_categories_old');
        Schema::rename('frontdesk_menus', 'frontdesk_menus_old');
        Schema::rename('frontdesk_inventories', 'frontdesk_inventories_old');
    }

    private function migrateData(): void
    {
        $branches = DB::table('branches')->get();

        foreach ($branches as $branch) {
            // Create main categories per branch
            $foodId = DB::table('item_categories')->insertGetId([
                'branch_id' => $branch->id,
                'parent_id' => null,
                'name' => 'Food',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $drinksId = DB::table('item_categories')->insertGetId([
                'branch_id' => $branch->id,
                'parent_id' => null,
                'name' => 'Drinks',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Migrate kitchen categories → subcategories under Food
            $kitchenCatMap = [];
            $kitchenCats = DB::table('menu_categories')->where('branch_id', $branch->id)->get();
            foreach ($kitchenCats as $cat) {
                $newId = DB::table('item_categories')->insertGetId([
                    'branch_id' => $branch->id,
                    'parent_id' => $foodId,
                    'name' => $cat->name,
                    'sort_order' => 0,
                    'created_at' => $cat->created_at,
                    'updated_at' => $cat->updated_at,
                ]);
                $kitchenCatMap[$cat->id] = $newId;
            }

            // Migrate pub categories → subcategories under Food (dedup by name)
            $pubCatMap = [];
            $pubCats = DB::table('pub_categories')->where('branch_id', $branch->id)->get();
            foreach ($pubCats as $cat) {
                $existing = DB::table('item_categories')
                    ->where('branch_id', $branch->id)
                    ->where('parent_id', $foodId)
                    ->where('name', $cat->name)
                    ->first();

                if ($existing) {
                    $pubCatMap[$cat->id] = $existing->id;
                } else {
                    $newId = DB::table('item_categories')->insertGetId([
                        'branch_id' => $branch->id,
                        'parent_id' => $foodId,
                        'name' => $cat->name,
                        'sort_order' => 0,
                        'created_at' => $cat->created_at,
                        'updated_at' => $cat->updated_at,
                    ]);
                    $pubCatMap[$cat->id] = $newId;
                }
            }

            // Migrate frontdesk categories → subcategories under Food (dedup by name)
            $fdCatMap = [];
            $fdCats = DB::table('frontdesk_categories')->where('branch_id', $branch->id)->get();
            foreach ($fdCats as $cat) {
                $existing = DB::table('item_categories')
                    ->where('branch_id', $branch->id)
                    ->where('parent_id', $foodId)
                    ->where('name', $cat->name)
                    ->first();

                if ($existing) {
                    $fdCatMap[$cat->id] = $existing->id;
                } else {
                    $newId = DB::table('item_categories')->insertGetId([
                        'branch_id' => $branch->id,
                        'parent_id' => $foodId,
                        'name' => $cat->name,
                        'sort_order' => 0,
                        'created_at' => $cat->created_at,
                        'updated_at' => $cat->updated_at,
                    ]);
                    $fdCatMap[$cat->id] = $newId;
                }
            }

            // Migrate kitchen menus → menu_items (department=1)
            $kitchenMenus = DB::table('menus')->where('branch_id', $branch->id)->get();
            foreach ($kitchenMenus as $menu) {
                $newMenuId = DB::table('menu_items')->insertGetId([
                    'branch_id' => $branch->id,
                    'department_id' => 1,
                    'category_id' => $kitchenCatMap[$menu->menu_category_id] ?? null,
                    'item_code' => $menu->item_code ?? null,
                    'name' => $menu->name,
                    'price' => (float) $menu->price,
                    'image' => null,
                    'is_available' => true,
                    'created_at' => $menu->created_at,
                    'updated_at' => $menu->updated_at,
                ]);

                $inv = DB::table('inventories')->where('menu_id', $menu->id)->where('branch_id', $branch->id)->first();
                if ($inv) {
                    DB::table('item_inventories')->insert([
                        'branch_id' => $branch->id,
                        'menu_item_id' => $newMenuId,
                        'number_of_serving' => $inv->number_of_serving,
                        'created_at' => $inv->created_at,
                        'updated_at' => $inv->updated_at,
                    ]);
                }
            }

            // Migrate pub menus → menu_items (department=2)
            $pubMenus = DB::table('pub_menus')->where('branch_id', $branch->id)->get();
            foreach ($pubMenus as $menu) {
                $newMenuId = DB::table('menu_items')->insertGetId([
                    'branch_id' => $branch->id,
                    'department_id' => 2,
                    'category_id' => $pubCatMap[$menu->pub_category_id] ?? null,
                    'item_code' => null,
                    'name' => $menu->name,
                    'price' => (float) $menu->price,
                    'image' => null,
                    'is_available' => true,
                    'created_at' => $menu->created_at,
                    'updated_at' => $menu->updated_at,
                ]);

                $inv = DB::table('pub_inventories')->where('pub_menu_id', $menu->id)->where('branch_id', $branch->id)->first();
                if ($inv) {
                    DB::table('item_inventories')->insert([
                        'branch_id' => $branch->id,
                        'menu_item_id' => $newMenuId,
                        'number_of_serving' => $inv->number_of_serving,
                        'created_at' => $inv->created_at,
                        'updated_at' => $inv->updated_at,
                    ]);
                }
            }

            // Migrate frontdesk menus → menu_items (department=3)
            $fdMenus = DB::table('frontdesk_menus')->where('branch_id', $branch->id)->get();
            foreach ($fdMenus as $menu) {
                $newMenuId = DB::table('menu_items')->insertGetId([
                    'branch_id' => $branch->id,
                    'department_id' => 3,
                    'category_id' => $fdCatMap[$menu->frontdesk_category_id] ?? null,
                    'item_code' => $menu->item_code ?? null,
                    'name' => $menu->name,
                    'price' => (float) $menu->price,
                    'image' => $menu->image ?? null,
                    'is_available' => true,
                    'created_at' => $menu->created_at,
                    'updated_at' => $menu->updated_at,
                ]);

                $inv = DB::table('frontdesk_inventories')->where('frontdesk_menu_id', $menu->id)->where('branch_id', $branch->id)->first();
                if ($inv) {
                    DB::table('item_inventories')->insert([
                        'branch_id' => $branch->id,
                        'menu_item_id' => $newMenuId,
                        'number_of_serving' => $inv->number_of_serving,
                        'created_at' => $inv->created_at,
                        'updated_at' => $inv->updated_at,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Restore old tables
        Schema::rename('menu_categories_old', 'menu_categories');
        Schema::rename('menus_old', 'menus');
        Schema::rename('inventories_old', 'inventories');
        Schema::rename('pub_categories_old', 'pub_categories');
        Schema::rename('pub_menus_old', 'pub_menus');
        Schema::rename('pub_inventories_old', 'pub_inventories');
        Schema::rename('frontdesk_categories_old', 'frontdesk_categories');
        Schema::rename('frontdesk_menus_old', 'frontdesk_menus');
        Schema::rename('frontdesk_inventories_old', 'frontdesk_inventories');

        Schema::dropIfExists('item_inventories');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('item_categories');
        Schema::dropIfExists('departments');
    }
};
