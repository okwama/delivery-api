<?php

namespace Database\Seeders;

use App\Models\Products\Brand;
use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brands')->delete();
        $json = File::get("database/data/brands.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            Brand::create(array(
                'brand' => $obj->brand,
                'title' => $obj->title ?? '',
                'headerOne' => $obj->headerOne ?? '',
                'url' => $obj->url ?? '',
                'category' => $obj->category ?? '',
                'pagedesc' => $obj->pagedesc ?? '',
                'description' => $obj->description ?? '',
                'country' => $obj->country ?? '',
            ));
        }
    }
}
