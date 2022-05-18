<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $exists = User::where('email', 'nairobidrinks@gmail.com')->first();
        if (!isset($exists)) {
            User::create([
                'name' => 'Nairobi Drinks',
                'email' => 'nairobidrinks@gmail.com',
                'phone' => '25473918900',
                'address' => 'Ruiru court 4,House no. 1',
                'location' => 'Ruiru',
                'role' => 'admin',
                'password' => bcrypt('nairobidrinks2021!!!')
            ]);
        } else {
            $exists->delete();
            User::create([
                'name' => 'NairobiDrinks',
                'email' => 'nairobidrinks@gmail.com',
                'phone' => '25473918900',
                'address' => 'Ruiru court 4,House no. 1',
                'location' => 'Ruiru',
                'role' => 'admin',
                'password' => bcrypt('nairobidrinks2021!!!')
            ]);
        }
    }
}
