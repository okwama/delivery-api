<?php

namespace App\Models\Products;

//use Spatie\Searchable\Searchable;
//use Spatie\Searchable\SearchResult;
use Jenssegers\Mongodb\Eloquent\Model;

//class Product extends Model implements  Searchable
class Product extends Model
{
    protected $guarded = [];
//    public function getSearchResult(): SearchResult
//    {
//        return new SearchResult($this, $this->name, $this->url);
//    }
}
