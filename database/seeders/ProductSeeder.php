<?php

namespace Database\Seeders;

use App\Models\Products\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->delete();
        $json = File::get("database/data/products.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            Product::create(array(
                'name' => $obj->name ?? '',
                'url' => $obj->url ?? '',
                'category' => $obj->category ?? '',
                'brand' => $obj->brand ?? '',
                'image' => $obj->image ?? '',
                'features' => $obj->features ?? '',
                'published' => $obj->published ?? '',
                'displayCategory' => $obj->displayCategory ?? '',
                'available' => $obj->available ?? '',
                'label' => $obj->label ?? '',
                'images' => $obj->images ?? '',
                'subcategory' => $obj->subcategory ?? '',
                'description' => $obj->description ?? '',
                'meta' => $obj->meta ?? '',
                'quantities' => $obj->quantities ?? '',
                'discount' => $obj->discount ?? '',
                'featured' => $obj->featured ?? '',
                'tags' => $obj->tags ?? '',
                'percentage' => $obj->percentage ?? '',
                'country' => $obj->country ?? '',
                'videoLink' => '',
            ));
        }
    }
}
