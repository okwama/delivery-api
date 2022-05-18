<?php

namespace App\Http\Controllers;

use App\Http\Resources\CarouselResource;
use App\Http\Resources\ProductResource;
use App\Models\Carousel;
use App\Models\Products\Brand;
use App\Models\Products\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HomePageController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $featured = ProductResource::collection(Product::query()->whereLabel('featured')
                ->latest()->take(12)->get()->values()->all());
            $on_offer = ProductResource::collection(Product::query()->whereLabel('on-offer')
                ->latest()->take(12)->get()->values()->all());
            $best_spirits = ProductResource::collection(Product::query()->whereLabel('best-selling-spirits')
                ->latest()->take(12)->get()->values()->all());
            $best_beers = ProductResource::collection(Product::query()->whereLabel('best-selling-beers')
                ->latest()->take(12)->get()->values()->all());
            $brands = Brand::all()->groupBy('category');
            $carousels = CarouselResource::collection(Carousel::latest()->orderBy('order', 'asc')->get());
            $result = [
                '$featured' => $featured,
                '$on_offer' => $on_offer,
                'best_spirits' => $best_spirits,
                'best_beers' => $best_beers,
                'brands' => $brands,
                'sliders' => $carousels,
            ];
            return $this->commonResponse('success', $result, Response::HTTP_OK);
        }
        catch(Exception $exception){
            return $this->commonResponse('failed', [], Response::HTTP_EXPECTATION_FAILED);

        }
    }
}
