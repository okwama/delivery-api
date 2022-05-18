<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(QuantitySeeder::class);
        $this->call(ArticleSeeder::class);
        $this->call(MetaSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(AdminSeeder::class);
    }
}
