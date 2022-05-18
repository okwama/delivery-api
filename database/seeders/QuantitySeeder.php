<?php

namespace Database\Seeders;

use App\Models\Products\ProductQuantity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class QuantitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_quantities')->delete();
        $json = File::get("database/data/quantities.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            ProductQuantity::create(array(
                'units' => $obj->units,
                'measurement' => $obj->measurement,
                'abbreviation' => $obj->abbreviation,
                'description' => $obj->description,
            ));
        }
    }
}
