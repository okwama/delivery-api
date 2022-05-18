<?php

namespace Database\Seeders;

use App\Models\Products\Order;
use Illuminate\Database\Seeder;

class ClearOrders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::query()->delete();
    }
}
