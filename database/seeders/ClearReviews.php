<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Seeder;

class ClearReviews extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Rating::query()->delete();
	}
}
