<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryResource;
use App\Models\Blog\Meta;
use App\Models\Country;
use App\Models\Products\Category;
use Symfony\Component\HttpFoundation\Response;

class CommonController extends Controller
{
    public function countries()
    {
        $countries = Country::all();
        return $this->commonResponse('Success', CountryResource::collection($countries), Response::HTTP_OK);
    }

    public function pageCategories()
    {
        $pages = [
            'home',
            'about-us',
            'order',
            'favorite',
            'contact',
            'pricelist',
            'our-terms',
            'faqs',
            'most-expensive',
            'corporate-purchase',
            'brands',
            'blog',
            'how-it-works',
            '404',
            'shop-by-country',
            'login',
            'register',
            'cart',
            'dashboard',
            'rating',
            'profile',
        ];
        $categories = Category::all()->pluck('name');
        $subcategory = Category::all()->pluck('subcategories')
            ->transform(function ($item) {
                return [
                    $item
                ];
            })->flatten();
        $page_categories = collect($pages)->merge($categories)->merge($subcategory)->unique()->flatten();
        $metas = Meta::all()->pluck('category');
        $free_categories = $page_categories->map(function ($item) use ($metas) {
            $collected = collect($metas);
            if ($collected->contains($item)) {
                return '';
            }
            return $item;
        })->filter()->flatten();
        $result=[
            'page_categories'=>$page_categories,
            'free_categories'=>$free_categories,
        ];
        //$free_categories
        return $this->commonResponse('Success', $result, Response::HTTP_OK);

    }
}
