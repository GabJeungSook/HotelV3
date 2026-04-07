<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $branches = Branch::all();

        foreach ($branches as $branch) {
            ItemCategory::firstOrCreate(
                ['branch_id' => $branch->id, 'parent_id' => null, 'name' => 'Food'],
                ['sort_order' => 1]
            );

            ItemCategory::firstOrCreate(
                ['branch_id' => $branch->id, 'parent_id' => null, 'name' => 'Drinks'],
                ['sort_order' => 2]
            );
        }
    }
}
