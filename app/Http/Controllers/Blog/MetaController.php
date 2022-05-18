<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog\Meta;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class MetaController extends Controller
{
    /**
     * list all meta tags
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $q=$request->search_query;
        $metas=Meta::query();
        if($request->has('search_query') && $request->filled('search_query')){
            $metas->where('category','like','%'.$q.'%')
                ->orWhere('pagetitle','like','%'.$q.'%')
                ->orWhere('title','like','%'.$q.'%');
        }
        $metas = $metas->paginate(10);
        return $this->commonResponse('Success', $metas, Response::HTTP_OK);

    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'headerOne' => 'required',
            'category' => 'required',
            'title' => 'required',
            'pagetitle' => 'required',
            'pagedesc' => 'nullable',
            'quotetitle' => 'nullable',
            'metadescription' => 'nullable',
            'footercontent' => 'nullable',
            'scripts' => 'nullable|array',
            'quotes' => 'nullable|array',
            'isCategory' => 'required|boolean',
            'website' => 'nullable',
            'highlight' => 'nullable',

        ]);
        if ($validator->fails()) {
            return $this->commonResponse('Error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                Meta::create($validator->validated());
                return $this->commonResponse('Success', 'Record created successfully', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('Error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('Error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }

    }

    /**
     * show single meta record
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $meta = Meta::find($id);
        if (is_null($meta) || !isset($meta)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        return $this->commonResponse('success', $meta, Response::HTTP_OK);
    }

    /**
     * show meta by category
     * @param $category
     * @return JsonResponse
     */
    public function getByCategory($category): JsonResponse
    {
        $meta = Meta::firstWhere('category', 'like', '%' . $category . '%');
        if (is_null($meta) || !isset($meta)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        return $this->commonResponse('success', $meta, Response::HTTP_OK);
    }
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'headerOne' => 'required',
            'category' => 'required',
            'title' => 'required',
            'pagetitle' => 'required',
            'pagedesc' => 'nullable',
            'quotetitle' => 'nullable',
            'metadescription' => 'nullable',
            'footercontent' => 'nullable',
            'scripts' => 'nullable|array',
            'quotes' => 'nullable|array',
            'isCategory' => 'required|boolean',
            'website' => 'nullable',
            'highlight' => 'nullable',

        ]);
        if ($validator->fails()) {
            return $this->commonResponse('Error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $meta = Meta::find($id);
                if (is_null($meta) || !isset($meta)) {
                    return $this->commonResponse('Error', [], Response::HTTP_NOT_FOUND);
                }
                $meta->update($validator->validated());
                return $this->commonResponse('Success', 'Record updated successfully!', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('Error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('Error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }

    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        $meta = Meta::find($id);
        if (is_null($meta) || !isset($meta)) {
            return $this->commonResponse('Error', 'Product quantity not found!', Response::HTTP_NOT_FOUND);
        }
        try {
            $meta->delete();
            return $this->commonResponse('Success', 'Record deleted!', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->commonResponse('Error', 'Could not delete meta,please try again!', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
