<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Products\Brand;
use App\Models\Products\Category;
use App\Models\Products\Product;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    /**
     *Get all brands
     */
    public function allBrands(): JsonResponse
    {
        $brands = Brand::all();
        return $this->commonResponse('Success', $brands, Response::HTTP_OK);
    }

    /**
     *Get all brands paginated
     * @param Request $request
     * @return JsonResponse
     */
    public function brands(Request $request): JsonResponse
    {
        $q=$request->search_query;
        $brands=Brand::query();
        if($request->has('search_query') && $request->filled('search_query')){
            $brands->where('name','like','%'.$q.'%')
            ->orWhere('title','like','%'.$q.'%');
        }
        $brands = $brands->paginate(10);
        return $this->commonResponse('Success', $brands, Response::HTTP_OK);
    }

    /**
     * Group brands by category
     * @return JsonResponse
     */
    public function groupByCategory(): JsonResponse
    {
        $brands = Brand::all()->groupBy('category');
        return $this->commonResponse('Success', $brands, Response::HTTP_OK);
    }

    /**
     * Show brand by Slug
     * @param $url
     * @return JsonResponse
     */
    public function brand($url): JsonResponse
    {
        $brand = Brand::where('url', $url)
        ->orWhere('brand',$url)
        ->first();
        if (is_null($brand) || !isset($brand)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        return $this->commonResponse('success', $brand, Response::HTTP_OK);
    }

    /**
     * Show brand by Name
     * @param $name
     * @return JsonResponse
     */
    public function brandByName($name): JsonResponse
    {
        $brand = Brand::firstWhere('brand', 'like', '%' . $name . '%');
        if (is_null($brand) || !isset($brand)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        return $this->commonResponse('success', $brand, Response::HTTP_OK);
    }

    /**
     * Show brand products by Slug
     * @param $url
     * @return JsonResponse
     */
    public function brandProducts($url): JsonResponse
    {
        $brand = Brand::firstWhere('url', $url);
        if (is_null($brand) || !isset($brand)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        $products = Product::where('brand', $brand->brand)->get() ?? [];
        return $this->commonResponse('success', ProductResource::collection($products), Response::HTTP_OK);
    }

    /**
     * Show brand products by Name
     * @param $name
     * @return JsonResponse
     */
    public function productsByName($name): JsonResponse
    {
        $brand = Brand::firstWhere('brand', 'like', '%' . $name . '%');
        if (is_null($brand) || !isset($brand)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        $products = Product::where('brand', $brand->brand)->get() ??
            Product::where('brand', $name)->get();
        return $this->commonResponse('success', ProductResource::collection($products), Response::HTTP_OK);
    }

    /**
     * Group brands by category
     * @param $category
     * @return JsonResponse
     */
    public function getByCategory($category): JsonResponse
    {
        $category = Category::firstWhere('url', $category);
        if (is_null($category) || !isset($category)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        $brands = Brand::where('category', $category->name)->get() ?? [];
        return $this->commonResponse('Success', $brands, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required|unique:brands',
            'title' => 'required',
            'category' => 'required',
            'headerOne' => 'required',
            'country' => 'nullable',
            'pagedesc' => 'nullable',
            'description' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $url = Str::slug($validator->validated()['brand'], '-');
                $data = collect($validator->validated())->put('url', $url);
                Brand::create($data->toArray());
                return $this->commonResponse('success', 'Brand created', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }

    }

    public function show($id): JsonResponse
    {
        $brand = Brand::find($id);
        if (is_null($brand) || !isset($brand)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        return $this->commonResponse('Success', $brand, Response::HTTP_OK);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required',
            'title' => 'required',
            'category' => 'required',
            'headerOne' => 'required',
            'country' => 'nullable',
            'pagedesc' => 'nullable',
            'description' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $brand = Brand::find($id);
                if (is_null($brand) || !isset($brand)) {
                    return $this->commonResponse('error', 'Brand not found!', Response::HTTP_NOT_FOUND);
                }
                $data = collect($validator->validated());
                $brand->update($data->toArray());
                return $this->commonResponse('Success', 'Brand updated', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('Error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('Error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }
    }

    public function delete($id): JsonResponse
    {
        $brand = Brand::find($id);
        if (is_null($brand) || !isset($brand)) {
            return $this->commonResponse('Error', 'Brand not found!', Response::HTTP_NOT_FOUND);
        }
        try {
            $brand->delete();
            return $this->commonResponse('Success', 'Record deleted!', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->commonResponse('Error', 'Could not delete brand', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
