<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products\Brand;
use App\Models\Products\Category;
use App\Models\Products\Order;
use App\Models\Products\Product;
use App\Models\Products\ProductQuantity;
use App\Models\Blog\Article;
use App\Models\Blog\Meta;
use App\Models\Carousel;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MigrateController extends Controller
{
    public function index()
    {
        self::categories();
        sleep(3);
        self::brands();
        sleep(3);
        self::products();
        sleep(2);
        self::articles();
        self::metas();
        self::quantities();
        self::orders();
        self::ratings();
        self::users();
        echo 'done';
    }
    //categories
    public static function categories()
    {
        $categories= Category::all()->toArray();
        foreach ($categories as $obj) {
            DB::connection('pgsql')->table('categories')->insert(array(
                'name' => $obj['name'] ?? '',
                'url' => $obj['url'] ?? '',
                'active' => $obj['active'] ?? '',
                'photo' => $obj['photo'] ?? '',
                'mobile_banner' => $obj['mobile_banner'] ?? '',
                'subcategories' => json_encode($obj['subcategories']),
                'menu' => $obj['menu'],
                'created_at' => $obj['created_at'],
                'updated_at' => $obj['updated_at'],
            ));
        }
    }
    //products
    public static function products()
    {
        $products= Product::all();
        foreach ($products as $obj) {
            DB::connection('pgsql')->table('products')->insert(array(
                'name' => $obj->name ?? '',
                'url' => $obj->url ?? '',
                'category' => $obj->category ?? '',
                'brand' => $obj->brand ?? '',
                'image' => $obj->image ?? '',
                'features' => json_encode($obj->features) ?? '',
                'published' => ($obj->published==1) ? true :false,
                'displayCategory' => json_encode($obj->displayCategory) ?? '',
                'available' => ($obj->available==1) ? true : false,
                'label' => json_encode($obj->label) ?? '',
                'images' => json_encode($obj->images) ?? '',
                'subcategory' => json_encode($obj->subcategory) ?? '',
                'description' => $obj->description ?? '',
                'meta' => $obj->meta ?? '',
                'quantities' => json_encode($obj->quantities) ?? '',
                'discount' => json_encode($obj->discount) ?? '',
                'featured' => ($obj->featured==1) ? true : false,
                'tags' => json_encode($obj->tags) ?? '',
                'percentage' => isset($obj->percentage) ?: 0,
                'country' => $obj->country ?? '',
                'videoLink' => '',
                'created_at' => $obj->created_at,
                'updated_at' => $obj->updated_at,
            ));
        }
    }
    //brands
    public static function brands()
    {
        $brands= Brand::all()->toArray();
        foreach ($brands as $obj) {
            DB::connection('pgsql')->table('brands')->insert(array(
                'brand' => $obj['brand'],
                'title' => $obj['title'] ?? '',
                'headerOne' => $obj['headerOne'] ?? '',
                'url' => $obj['url'] ??  '',
                'category' => $obj['category'] ?? '',
                'pagedesc' => $obj['pagedesc'] ?? '',
                'description' => $obj['description'] ?? '',
                'country' => $obj['country'] ?? '',
                'created_at' => $obj['created_at'],
                'updated_at' => $obj['updated_at'],
            ));
        }
    }
    //articles
    public static function articles()
    {
        $articles=Article::all();
        foreach ($articles as $obj) {
            DB::connection('pgsql')->table('articles')->insert(array(
                'title' => $obj->title,
                'meta' => $obj->meta,
                'url' => $obj->url,
                'body' => $obj->body,
                'image' => $obj->image ?? '',
                'tags' => json_encode($obj->tags),
                'created_at' => $obj->created_at,
                'updated_at' => $obj->updated_at,
            ));
        }
    }
    //orders
    public static function orders()
    {
        $orders=Order::all();
        foreach ($orders as $obj) {
            DB::connection('pgsql')->table('orders')->insert(array(
                "instructions" => $obj->instructions,
                "name" => $obj->name,
                "phone" => $obj->phone,
                "email" => $obj->email,
                "location" => $obj->location,
                "amountPaid" => $obj->amountPaid,
                "discountApplied" => $obj->discountApplied,
                "paymentOption" => $obj->paymentOption,
                "total" => $obj->total,
                "products" => json_encode($obj->products),
                "deliveryDate" => $obj->deliveryDate,
                "scheduleDate" => $obj->deliveryDate,
                "dateShipped" => $obj->dateShipped,
                "pending" => $obj->pending,
                "rejected" => $obj->rejected,
                "handled" => $obj->handled,
                "approved" => $obj->approved,
                "confirmed" => $obj->confirmed,
                "paid" => $obj->paid,
                "scheduled" => $obj->scheduled,
                "shipped" => $obj->shipped,
                "orderCategory" => $obj->orderCategory,
                "medium" => $obj->medium,
                "orderNo" => $obj->orderNo,
                "placedOn" => $obj->placedOn,
                "road"=>$obj->road ?? '',
                "house"=>$obj->house ?? '',
                "street"=>$obj->street ?? '',
                "building"=> $obj->building ?? '',
                "reason"=> $obj->reason ?? '',
                "created_at" => $obj->created_at,
                "updated_at" => $obj->updated_at,
                ));
        }
    }
    
    //metas
    public static function metas()
    {
        $metas=Meta::all();
        foreach ($metas as $obj) {
            DB::connection('pgsql')->table('metas')->insert(array(
                'headerOne' => $obj->headerOne ?? '',
                'category' => $obj->category ?? '',
                'title' => $obj->title ?? '',
                'pagetitle' => $obj->pagetitle ?? '',
                'pagedesc' => $obj->pagedesc ?? '',
                'quotetitle' => $obj->quotetitle ?? '',
                'metadescription' => $obj->metadescription ?? '',
                'footercontent' => $obj->footercontent ?? '',
                'scripts' => json_encode($obj->scritps) ?? '',
                'quotes' => json_encode($obj->quotes) ?? '',
                'isCategory' => $obj->isCategory ?? '',
                'website' => $obj->website ?? '',
                'highlight' => $obj->highlight ?? '',
                'created_at' => $obj->created_at,
                'updated_at' => $obj->updated_at,
            ));
        }
    }
    //product_quantities
    public static function quantities()
    {
        $quantities=ProductQuantity::all();
        foreach ($quantities as $obj) {
            DB::connection('pgsql')->table('product_quantities')->insert(array(
                'units' => $obj->units,
                'measurement' => $obj->measurement,
                'abbreviation' => $obj->abbreviation ?? '',
                'description' => $obj->description ?? '',
                'created_at' => $obj->created_at,
                'updated_at' => $obj->updated_at,
            ));
        }
    }
    //ratings
    public static function ratings()
    {
        $ratings=Rating::all();
        foreach ($ratings as $obj) {
            DB::connection('pgsql')->table('ratings')->insert(array(
                    'stars' =>  $obj->stars,
                    'review' =>  $obj->review,
                    'email' => $obj->email,
                    'name' =>  $obj->name,
                    'phone' =>  $obj->phone,
                    'productId' =>  $obj->productId,
                    'product' =>  json_encode($obj->product),
                    'status' =>  ($obj->status==1) ? true : false,
                    'created_at' => $obj->created_at,
                    'updated_at' => $obj->updated_at,
            ));
        }
    }
    public static function users()
    {
        $users=User::all();
        DB::connection('pgsql')->table('users')->truncate();

        foreach ($users as $obj) {
            DB::connection('pgsql')->table('users')->insert(array(
                    "name" => $obj->name,
                    "email" => $obj->email,
                    "phone" =>$obj->phone,
                    "address" => $obj->address,
                    "location" =>$obj->location,
                    "role" => $obj->role,
                    "password" => $obj->password,
                    "email_verified_at" => $obj->email_verified_at,
                    "created_at" => $obj->created_at,
                    "updated_at" => $obj->updated_at,
            ));
        }
    }
    //users
}
