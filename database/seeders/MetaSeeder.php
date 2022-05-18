<?php

namespace Database\Seeders;

use App\Models\Blog\Meta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('metas')->delete();
        $json = File::get("database/data/metas.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            Meta::create(array(
                'headerOne' => $obj->headerOne ?? '',
                'category' => $obj->category ?? '',
                'title' => $obj->title ?? '',
                'pagetitle' => $obj->pagetitle ?? '',
                'pagedesc' => $obj->pagedesc ?? '',
                'quotetitle' => $obj->quotetitle ?? '',
                'metadescription' => $obj->metadescription ?? '',
                'footercontent' => $obj->footercontent ?? '',
                'scripts' => $obj->scritps ?? '',
                'quotes' => $obj->quotes ?? '',
                'isCategory' => $obj->isCategory ?? '',
                'website' => $obj->website ?? '',
                'highlight' => $obj->highlight ?? '',
            ));
        }
    }
}
