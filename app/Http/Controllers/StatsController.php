<?php

namespace App\Http\Controllers;

use App\Models\Products\Brand;
use App\Models\Products\Category;
use App\Models\Products\Order;
use App\Models\Products\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::count();
        $brands = Brand::count();
        $categories = Category::count();
        $todayOrders = Order::query()->select(['placedOn'])->get()
        ->filter(function($item){
            return Carbon::parse(@$item->placedOn)->format('Y-m-d')===date('Y-m-d');
           
        })->count();
        $orders = Order::count();
        $result = [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'orders' => $orders,
            'todayOrders' => $todayOrders,
        ];
        return $this->commonResponse('success', $result, Response::HTTP_OK);
    }
}
