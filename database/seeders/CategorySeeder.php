<?php

namespace Database\Seeders;

use App\Models\Products\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->delete();
        $json = File::get("database/data/categories.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            $subs = [];
            foreach ($obj->subcategories as $sub) {
                $subs[] = [
                    'name' => $sub->name ?? ''
                ];
            }
            Category::create(array(
                'name' => $obj->name ?? '',
                'url' => $obj->url ?? '',
                'active' => $obj->active ?? '',
                'photo' => '',
                'subcategories' => $subs,
                'menu' => $obj->menu,
            ));
        }
    }
}
