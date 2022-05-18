<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Products\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class AnalyticsController extends Controller
{
    public function expensiveDrinks(): JsonResponse
    {
        $categories =
            [
                'Whisky',
                'Champagne',
                'Cognac',
                'Wine',
                'Brandy',
                'Vodka',
                'Tequila',
                'Liqueur',
                'Gin',
                'Rum',
            ];
        $raw_categories = [];
        foreach ($categories as $cat) {
            $raw_categories[] = Product::where('category', 'like', '%'.$cat.'%')
                ->get()
                ->sortByDesc(function ($item) {
                    return
                        collect(json_decode(json_encode($item->quantities), true))->first()['discount'] ?? 0
                ;
                })->transform(function ($product) {
                    return new ProductResource($product);
                })->take(12)->values()->all();
        }
        $raw_categories=collect($raw_categories)->flatten()->groupBy('category');
        // $real_categories
        return $this->commonResponse('success', $raw_categories, Response::HTTP_OK);
    }

    /**
     * Show price list
     */
    public function priceList(): JsonResponse
    {
        $categories =
            [
                'Whisky',
                'Champagne',
                'Cognac',
                'Wine',
                'Brandy',
                'Vodka',
                'Tequila',
                'Liqueur',
                'Gin',
                'Rum',
                'Beer',
            ];
        $raw_categories = [];
        $mapped_categories = [];
        foreach ($categories as $cat) {
            $raw_categories[] = Product::query()->where('category', 'like', '%'.$cat.'%')->get()
                ->map(function ($item) {
                    return $item;
                })->all();
        }
        foreach (collect($raw_categories)->flatten()  as $key=> $item) {
            foreach ($item['quantities'] as $num) {
                $mapped_categories[]=[
                    '_id'=>$item['_id'],
                    'name'=>$item['name'],
                    'category'=>$item['category'],
                    'available'=>$item['available'],
                    'url'=>$item['url'],
                    'discount'=>$num['discount'] ?? 0,
                    'price'=>$num['price'] ?? 0,
                    'quantity'=>$num['quantity'] ?? '',
                ];
            }
        }
        $raw_categories=collect($mapped_categories)->sortByDesc('discount')->groupBy('category') ?? [];
        // $real_categories
        return $this->commonResponse('success', $raw_categories, Response::HTTP_OK);
    }
    //loop through product
    private function loopProduct($product): array
    {
        $data=[];
        foreach ($product->quantities as $key=> $quantity) {
            $data[]=[
                'category'=>$key['category'],
                'quantity'=>$quantity['quantity'],
                'discount'=>$quantity['discount'],
                'price'=>$quantity['price'],

            ];
        }
        return $data;
    }
    //get product countries
    public function productCountries(): JsonResponse
    {
        $products=Product::query()->get()->pluck('country');
        $countries=$products->filter(function ($item) {
            return $item!='undefined' && $item!='';
        })->map(function ($value) {
            return ltrim($value);
        })
        ->unique()->values()->all();
        return $this->commonResponse('success', $countries, Response::HTTP_OK);
    }
    //get products per  country
    public function countryProducts($country): JsonResponse
    {
        $categories =
            [
                'Whisky',
                'Champagne',
                'Cognac',
                'Wine',
                'Brandy',
                'Vodka',
                'Tequila',
                'Liqueur',
                'Gin',
                'Rum',
                'Beer',
            ];
        $raw_products = [];
        foreach ($categories as $cat) {
            $raw_products[] = Product::query()->where(function ($fetch) use ($cat, $country) {
                $fetch->where('category', 'like', '%'.$cat.'%')
                      ->where('country', 'like', '%'.$country.'%');
            })->get()
                ->map(function ($item) {
                    return new ProductResource($item);
                })->all();
        }
        $products=collect($raw_products)->flatten()->groupBy('category');
        return $this->commonResponse('success', $products, Response::HTTP_OK);
    }
}
