<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Products\Brand;
use App\Models\Products\Category;
use App\Models\Products\Product;
use Cache;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * List categories
     * @return JsonResponse
     */
    public function allCategories(): JsonResponse
    {
        $categories = Category::all()->transform(function ($item) {
            return new CategoryResource($item);
        })->paginate(10);
        return $this->commonResponse('success', $categories, Response::HTTP_OK);
    }

    /**
     * List categories
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        $categories = Cache::rememberForever('categories', function () {
            return Category::all();
        });
        return $this->commonResponse('success', CategoryResource::collection($categories), Response::HTTP_OK);
    }

    /**
     * Insert category into database
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'menu' => 'required',
            'active' => 'required',
            'photo' => 'nullable',
            'fileName' => 'nullable',
            'mobile_banner' => 'nullable',
            'mobile_file' => 'nullable',
            'subcategories' => 'required|array',
        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $subs = collect($request->subcategories);
                if ($subs->isEmpty()) {
                    $subcategories = [];
                } else {
                    $subcategories = $subs->map(function ($item) {
                        return [
                            'name' => Str::slug($item['name'], '-')
                        ];
                    })->values()->all();
                }
                $data = collect($validator->validated());
                //photo
                if ($request->has('fileName') && $request->filled('fileName')) {
                    $raw_file_name = explode('.', $request->photo);
                    $extension = end($raw_file_name);
                    $uploadedString = $data['fileName'];  // your base64 encoded
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uploadedString));
                    $imageName = Str::random(3) . '-' . $raw_file_name[0] . '.' . $extension;
                    file_put_contents(public_path() . '/uploads/' . $imageName, $image);
                    $data['photo'] = $imageName;
                } else {
                    $data['photo'] = '';
                }
                $data->forget('fileName');
                //mobile banner
                if ($request->has('mobile_file') && $request->filled('mobile_file')) {
                    $raw_file_name = explode('.', $request->mobile_banner);
                    $extension = end($raw_file_name);
                    $uploadedString = $data['mobile_file'];  // your base64 encoded
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uploadedString));
                    $imageName = Str::random(3) . '-' . $raw_file_name[0] . '.' . $extension;
                    file_put_contents(public_path() . '/uploads/' . $imageName, $image);
                    $data['mobile_banner'] = $imageName;
                } else {
                    $data['mobile_banner'] = '';
                }
                $data->forget('mobile_file');
                // end mobile banner
                $data['url'] = Str::slug($data['name'], '-');
                $data['subcategories'] = $subcategories;
                $final_data = $data->toArray();
                Category::create($final_data);
                Cache::flush();
                return $this->commonResponse('success', 'category created', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }
    }

    /**
     * Get category by name
     * @param $name
     * @return JsonResponse
     */
    public function getByName($name): JsonResponse
    {
        $category = Category::query()->where('name', 'like', '%' . $name . '%')->first();
        if (is_null($category) || !isset($category)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        return $this->commonResponse('success', new CategoryResource($category), Response::HTTP_OK);
    }

    /**
     * Get category by ID
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $category = Category::find($id);
        if (is_null($category) || !isset($category)) {
            return $this->commonResponse('error', [], Response::HTTP_NOT_FOUND);
        }
        return $this->commonResponse('success', new CategoryResource($category), Response::HTTP_OK);
    }

    /**
     * Get category by slug
     * @param $url
     * @return JsonResponse
     */
    public function getProductsByCategorySlug($url): JsonResponse
    {
        $category = Category::where('url', $url)->first();
        if (is_null($category) || !isset($category)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        $products = Product::where('category', $category->name)->get();
        return $this->commonResponse('success', ProductResource::collection($products), Response::HTTP_OK);
    }

    /**
     * Get category by slug/url
     * @param $url
     * @return JsonResponse
     */
    public function getBySlug($url): JsonResponse
    {
        $category = Category::query()->where('url', 'like', '%' . $url . '%')->first();
        if (is_null($category) || !isset($category)) {
            return $this->commonResponse('error', [], Response::HTTP_NOT_FOUND);
        }
        $brands = Brand::where('category', $category->name)->get() ?? [];
        $result = [
            'category' => new CategoryResource($category),
            'brands' => $brands,
        ];
        return $this->commonResponse('success', $result, Response::HTTP_OK);
    }

    /**
     * Get category by subcategory
     * @param $subcategory
     * @return JsonResponse
     */
    public function getBySubcategory($subcategory): JsonResponse
    {
        $category = Category::query()->where('subcategories.name', 'like', '%' . $subcategory . '%')->first();
        if (is_null($category) || !isset($category)) {
            return $this->commonResponse('error', [], Response::HTTP_NOT_FOUND);
        }
        return $this->commonResponse('success', new CategoryResource($category), Response::HTTP_OK);
    }

    /**
     * Show product by menu
     * @return JsonResponse
     */
    public function getByMenu(): JsonResponse
    {
        $categories = Cache::rememberForever('category_menus', function () {
            $raw_categories = Category::query()->get();
            $order = ['spirits', 'wines', 'beer', 'more'];
            $fetch_all = $raw_categories->sort(function ($a, $b) use ($order) {
                $pos_a = array_search($a->menu, $order);
                $pos_b = array_search($b->menu, $order);
                return $pos_a - $pos_b;
            })->values()->all();
            return collect($fetch_all)->groupBy('menu');
        });
        return $this->commonResponse('success', $categories, Response::HTTP_OK);
    }

    /**
     * Show product by menu
     * @return JsonResponse
     */
    public function liquorMenu(): JsonResponse
    {
        $menus =
            [
                'whisky',
                'champagne',
                'cognac',
                'wine',
                'brandy',
                'vodka',
                'tequila',
                'liqueur',
                'gin',
                'rum',
                'beer',
                'offers',
                'gifts',
            ];

        $categories = Category::query()->whereIn('name', $menus)->get();
        $fetch_all = $categories->sort(function ($a, $b) use ($menus) {
            $pos_a = array_search($a->name, $menus);
            $pos_b = array_search($b->name, $menus);
            return $pos_a - $pos_b;
        })->values()->all();
        $categories = collect($fetch_all);
        return $this->commonResponse('success', $categories, Response::HTTP_OK);

    }

    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'menu' => 'required',
            'active' => 'required',
            'photo' => 'nullable',
            'fileName' => 'nullable',
            'mobile_banner' => 'nullable',
            'mobile_file' => 'nullable',
            'subcategories' => 'required|array',
        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $category = Category::find($id);
                if (is_null($category) || !isset($category)) {
                    return $this->commonResponse('error', 'Category not found!', Response::HTTP_NOT_FOUND);
                }
                $subs = collect($request->subcategories);
                if ($subs->isEmpty()) {
                    $subcategories = [];
                } else {
                    $subcategories = $subs->map(function ($item) {
                        return [
                            'name' => Str::slug($item['name'], '-')
                        ];
                    })->values()->all();
                }
                $data = collect($validator->validated());
                //photo
                if ($request->has('fileName') && $request->filled('fileName')) {
                    $raw_file_name = explode('.', $request->photo);
                    $extension = end($raw_file_name);
                    $uploadedString = $data['fileName'];  // your base64 encoded
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uploadedString));
                    $imageName = Str::random(3) . '-' . $raw_file_name[0] . '.' . $extension;
                    file_put_contents(public_path() . '/uploads/' . $imageName, $image);
                    $data['photo'] = $imageName;
                } else {
                    $data['photo'] = '';
                }
                $data->forget('fileName');
                //mobile banner
                if ($request->has('mobile_file') && $request->filled('mobile_file')) {
                    $raw_file_name = explode('.', $request->mobile_banner);
                    $extension = end($raw_file_name);
                    $uploadedString = $data['mobile_file'];  // your base64 encoded
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uploadedString));
                    $imageName = Str::random(3) . '-' . $raw_file_name[0] . '.' . $extension;
                    file_put_contents(public_path() . '/uploads/' . $imageName, $image);
                    $data['mobile_banner'] = $imageName;
                } else {
                    $data['mobile_banner'] = '';
                }
                $data->forget('mobile_file');
                $data['subcategories'] = $subcategories;
                $category->update($data->toArray());
                Cache::flush();
                return $this->commonResponse('success', 'category updated', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }
    }

    public function delete($id): JsonResponse
    {
        $category = Category::where('_id', $id)->first();
        if (is_null($category)) {
            return $this->commonResponse('error', 'Category not found!', Response::HTTP_NOT_FOUND);
        }
        try {
            // if (!is_null($category->photo) && isset($category->photo)) {
            //     $image_path = public_path('uploads/' . $category->photo);
            //     if (File::exists($image_path)) {
            //         unlink($image_path);
            //     }
            // }
            $category->delete();
            return $this->commonResponse('success', 'Category deleted!', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->commonResponse('error', $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }
}
