<?php

namespace Database\Seeders;

use App\Models\Blog\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('articles')->delete();
        $json = File::get("database/data/articles.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            Article::create(array(
                'title' => $obj->title,
                'meta' => $obj->meta,
                'url' => $obj->url,
                'body' => $obj->body,
                'image' => $obj->image ?? '',
                'images' => $obj->images ?? '',
                'website' => $obj->website,
                'ads' => $obj->ads,
                'keywords' => $obj->keywords,
            ));
        }
    }
}
