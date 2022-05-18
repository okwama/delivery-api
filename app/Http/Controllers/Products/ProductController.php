<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\RatingResource;
use App\Models\Products\Brand;
use App\Models\Products\Category;
use App\Models\Products\Product;
use App\Models\Rating;
use Cache;
use Exception;
use File;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function randomProducts(): JsonResponse
    {
        $raw_products = Product::all();
        $products = $raw_products->shuffle()->take(12);
        return $this->commonResponse('success', ProductResource::collection($products), Response::HTTP_OK);
    }

    public function allProducts(Request $request): JsonResponse
    {
        $brand = $request->brand;
        $category = $request->category;
        $subcategory = $request->subcategory;
        $products = Product::query();
        if ($request->has('brand') && $request->filled('brand')) {
            $products->where('brand', 'like', '%' . $brand . '%');
        }
        if ($request->has('category') && $request->filled('category')) {
            $products->where('category', 'like', '%' . $category . '%');
        }
        if ($request->has('subcategory') && $request->filled('subcategory')) {
            $products->whereSubcategory($subcategory);
        }
        $products = ProductResource::collection($products->get());
        return $this->commonResponse('success', $products, Response::HTTP_OK);
    }

    /**
     * List all products
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $sort_by = $request->sort_by;
        $brand = $request->brand;
        $category = $request->category;
        $subcategory = $request->subcategory;
        $products = Product::query();
        if ($request->has('brand') && $request->filled('brand')) {
            $products->where('brand', $brand);
        }
        if ($request->has('category') && $request->filled('category')) {
            $products->where('category', $category);
        }
        if ($request->has('subcategory') && $request->filled('subcategory')) {
            $products->whereSubcategory($subcategory);
        }
        $products = $products->get();
        //apply order filters
        if ($request->has('sort_by') || $request->filled('sort_by')) {
            if ($sort_by === 'alcohol_low') {
                $products = $products->sortBy('percentage');
            }
            if ($sort_by === 'alcohol_high') {
                $products = $products->sortByDesc('percentage');
            }
            if ($sort_by === 'price_low') {
                $products = $products->sortBy(function ($item) {
                    return $item->quantities[0]['discount'];
                });
            }
            if ($sort_by === 'price_high') {
                $products = $products->sortByDesc(function ($item) {
                    return $item->quantities[0]['discount'];
                });
            }
        }
        $products = collect(ProductResource::collection($products))->paginate(12);
        return $this->commonResponse('success', $products, Response::HTTP_OK);
    }

    public function productsByLabel(Request $request): JsonResponse
    {
        $label = $request->label;
        $products = Product::query();
        if ($request->has('label') || $request->filled('label')) {
            $products->whereLabel($label);
        }
        $products = collect(ProductResource::collection($products->take(12)->get()))->toArray();
        return $this->commonResponse('success', $products, Response::HTTP_OK);
    }

    /**
     * Search products
     * @param Request $request
     * @return JsonResponse
     */

    public function searchProducts(Request $request): JsonResponse
    {
        $q = $request->input('query');
        $products = Product::query()
            ->where('name', 'like', '%' . $q . '%')
            ->get();
        if ($products->isNotEmpty()) {
            $subs = $products->pluck('subcategory')->flatten()
                ->filter(function ($item) use ($q) {
                    if (strpos($item, $q) !== false) {
                        return $item;
                    } else {
                        return '';
                    }
                })->unique()
                ->values()->all();
            $subcategories = collect($subs)->map(function ($item) {
                if (!$this->getCategory($item)) {
                    return [];
                } else {
                    return [
                        'subcategory' => $item,
                        'category' => ($this->getCategory($item)) ? $this->getCategory($item)->name : '',
                        'category_url' => $this->getCategory($item)->url,
                    ];
                }
            })->filter()->values()->all();
            $brands = Brand::query()->where('brand', 'like', '%' . $q . '%')
                    ->get(['_id', 'brand', 'url']) ?? [];
            $results = [
                'products' => ProductResource::collection($products),
                'brands' => $brands,
                'subcategories' => $subcategories,
            ];
        }
        else{
            $results = [
                'products' => [],
                'brands' => [],
                'subcategories' => [],
            ];
        }
        return $this->commonResponse('success', $results, Response::HTTP_OK);


    }

    //Search category by subcategory name
    private function getCategory($subcategory)
    {
        return Category::where('subcategories.name', $subcategory)
                ->first() ?? [];
    }
    // end search category

    /**
     * Create product
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'url' => 'nullable',
            'category' => 'required',
            'brand' => 'nullable',
            'image' => 'nullable',
            'fileName' => 'nullable',
            'features' => 'nullable|array',
            'published' => 'nullable|boolean',
            'displayCategory' => 'nullable|array',
            'available' => 'nullable|boolean',
            'label' => 'nullable|array',
            'images' => 'nullable|array',
            'subcategory' => 'nullable|array',
            'description' => 'nullable',
            'meta' => 'nullable',
            'quantities' => 'nullable|array',
            'discount' => 'nullable|array',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'percentage' => 'required|numeric',
            'country' => 'nullable',
            'videoLink' => 'nullable',

        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $data = collect($validator->validated());
                if ($request->has('fileName') && $request->filled('fileName')) {
                    $raw_file_name = explode('.', $request->image);
                    $extension = end($raw_file_name);
                    $uploadedString = $data['fileName']; // your base64 encoded
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uploadedString));
                    $imageName = Str::random(3) . '-' . $raw_file_name[0] . '.' . $extension;
                    file_put_contents(public_path() . '/uploads/' . $imageName, $image);
                    $data['image'] = $imageName;
                } else {
                    $data['image'] = '';
                }
                $data->forget('fileName');
                $data['url'] = Str::slug($data['name']);
                $final_data = $data->toArray();
                Product::create($final_data);
                Cache::flush();
                return $this->commonResponse('success', 'Record created successfully', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }

    /**
     * Show product
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $product = Product::find($id);
        if (is_null($product) || !isset($product)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        return $this->commonResponse('success', new ProductResource($product), Response::HTTP_OK);
    }

    /**
     * Show product by Slug
     * @param $url
     * @return JsonResponse
     */
    public function product($url): JsonResponse
    {
        $product = Product::firstWhere('url', $url);
        if (!$product) {
            $result = [
                'reviews' => [],
                'category' => [],
                'families' => [],
                'brand' => [],
                'product' => [],
                'related' => [],
            ];

            return $this->commonResponse('error', $result, Response::HTTP_OK);
        } else {
            $category = Category::firstWhere('name', 'like', '%' . $product->category . '%') ?? [];
            $brand = Brand::firstWhere('brand', 'like', '%' . $product->brand . '%') ?? [];
            if (is_null($brand) || empty($brand)) {
                $families = [];
            } else {
                $families = Product::query()->where('_id', '<>', $product->_id)
                        ->where('brand', $brand->brand)->get() ?? [];
            }
            $related = Product::query()->where('brand', $product->brand)
                ->orWhere('category', $product->category)->take(4)->get();
            // Reviews
            $reviews = Rating::where('productId', $product->_id)
                ->whereStatus(1)->get();
            $result = [
                'reviews' => RatingResource::collection($reviews),
                'category' => $category,
                'families' => $families,
                'brand' => $brand,
                'product' => new ProductResource($product),
                'related' => ProductResource::collection($related),
            ];
            return $this->commonResponse('success', $result, Response::HTTP_OK);
        }
    }

    /**
     * Show product by Slug
     * @param $url
     * @return JsonResponse
     */
    public function relatedProducts($url): JsonResponse
    {
        $product = Product::where('url', $url)->first();
        if (is_null($product) || !isset($product)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        $products = Product::where([
            ['category', $product->category],
            ['_id', '<>', $product->_id],
        ])->get()->take(4);
        return $this->commonResponse('success', ProductResource::collection($products), Response::HTTP_OK);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category' => 'required',
            'brand' => 'nullable',
            'image' => 'nullable',
            'features' => 'nullable|array',
            'published' => 'nullable|boolean',
            'displayCategory' => 'nullable|array',
            'available' => 'nullable|boolean',
            'fileName' => 'nullable',
            'label' => 'nullable|array',
            'images' => 'nullable|array',
            'subcategory' => 'nullable|array',
            'description' => 'nullable',
            'meta' => 'nullable',
            'quantities' => 'nullable|array',
            'discount' => 'nullable|array',
            'featured' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'percentage' => 'required|numeric',
            'country' => 'nullable',
            'videoLink' => 'nullable',

        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $product = Product::find($id);
                if (is_null($product) || !isset($product)) {
                    return $this->commonResponse('error', 'Product not found!', Response::HTTP_NOT_FOUND);
                }
                $data = collect($validator->validated());
                if ($request->has('fileName') && $request->filled('fileName')) {
                    $raw_file_name = explode('.', $request->image);
                    $extension = end($raw_file_name);
                    $uploadedString = $data['fileName']; // your base64 encoded
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uploadedString));
                    $imageName = Str::random(3) . '-' . $raw_file_name[0] . '.' . $extension;
                    file_put_contents(public_path() . '/uploads/' . $imageName, $image);
                    $data['image'] = $imageName;
                } else {
                    unset($data['image']);
                    //$data['image'] = '';
                }
                $data->forget('fileName');
                $final_data = $data->toArray();
                $product->update($final_data);
                Cache::flush();
                return $this->commonResponse('success', 'Record updated successfully', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }

    /**
     * Delete product
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        $product = Product::find($id);
        if (is_null($product) || !isset($product)) {
            return $this->commonResponse('error', 'Product not found!', Response::HTTP_NOT_FOUND);
        }
        try {
            $image_path = public_path('uploads/' . $product->image);
            if (File::exists($image_path)) {
                //File::delete($image_path);
                unlink($image_path);
            }
            $product->delete();
            Cache::flush();
            return $this->commonResponse('success', 'Record deleted!', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->commonResponse('error', 'Could not delete product', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove item from quantities
     * @param Request $request
     * @return JsonResponse
     */
    public function removeQuantity(Request $request): JsonResponse
    {
        try {
            $quantity = $request->quantity;
            $product = Product::where('_id', $request->productId)->first();
            $quantities = collect($product->quantities)->filter(function ($item) use ($quantity) {
                return $item['quantity'] != $quantity;
            });
            $product->update([
                'quantities' => $quantities->values()->all(),
            ]);
            return $this->commonResponse('success', 'Quantity removed successfully!', Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->commonResponse('error', 'Quantity could not be removed!', Response::HTTP_NOT_FOUND);
        }
    }
}
