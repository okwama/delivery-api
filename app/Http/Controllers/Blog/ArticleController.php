<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Blog\Article;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    /**
     * show all articles paginated
     * list articles
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $q=$request->search_query;
        $articles=Article::query()->latest();
        if($request->has('search_query') && $request->filled('search_query')){
            $articles->where('title','like','%'.$q.'%');
        }
        $articles = $articles->paginate(40);
        return $this->commonResponse('success', ArticleResource::collection($articles)->response()->getData(true), Response::HTTP_OK);

    }
    /**
     * show all articles
     * list articles
     */
    public function index(): JsonResponse
    {
        $articles = Article::all();
        return $this->commonResponse('success', ArticleResource::collection($articles), Response::HTTP_OK);

    }

    /**
     * Show article by Slug
     * @param $url
     * @return JsonResponse
     */
    public function article($url): JsonResponse
    {
        $article = Article::firstWhere('url', $url);
        if (is_null($article) || !isset($article)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        return $this->commonResponse('success', new ArticleResource($article), Response::HTTP_OK);
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'meta' => 'required',
            'body' => 'nullable',
            'image' => 'nullable',
            'fileName' => 'nullable',
            'tags' => 'nullable|array',

        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $data = $validator->validated();
                $data['url'] = Str::slug($data['title'], '-');
                //photo
                if ($request->has('fileName') && $request->filled('fileName')) {
                    $raw_file_name = explode('.', $request->image);
                    $extension = end($raw_file_name);
                    $uploadedString = $data['fileName'];  // your base64 encoded
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uploadedString));
                    $imageName = Str::random(3) . '-' . $raw_file_name[0] . '.' . $extension;
                    file_put_contents(public_path() . '/uploads/blog/' . $imageName, $image);
                    $data['image'] = $imageName;
                } else {
                    $data['image'] = '';
                }
                $data = collect($data);
                $data->forget('fileName');
                Article::create($data->toArray());
                return $this->commonResponse('Success', 'Record created successfully', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }
    }

    /**
     * show single article record
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $article = Article::find($id);
        if (is_null($article) || !isset($article)) {
            return $this->commonResponse('error', 'Article not found!', Response::HTTP_NOT_FOUND);
        }
        return $this->commonResponse('Success', new ArticleResource($article), Response::HTTP_OK);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'meta' => 'required',
            'body' => 'nullable',
            'image' => 'nullable',
            'fileName' => 'nullable',
            'tags' => 'nullable|array',

        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $article = Article::find($id);
                if (is_null($article) || !isset($article)) {
                    return $this->commonResponse('error', 'Article not found!', Response::HTTP_NOT_FOUND);
                }
                $data = $validator->validated();
                //photo
                if ($request->has('fileName') && $request->filled('fileName')) {
                    $raw_file_name = explode('.', $request->image);
                    $extension = end($raw_file_name);
                    $uploadedString = $data['fileName'];  // your base64 encoded
                    $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uploadedString));
                    $imageName = Str::random(3) . '-' . $raw_file_name[0] . '.' . $extension;
                    file_put_contents(public_path() . '/uploads/blog/' . $imageName, $image);
                    $data['image'] = $imageName;
                } else {
                    $data['image'] = '';
                }
                $data = collect($data);
                $data->forget('fileName');
                $article->update($data->toArray());
                return $this->commonResponse('success', 'Record updated successfully', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }
    }

    /**
     * delete article
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        $article = Article::find($id);
        if (is_null($article) || !isset($article)) {
            return $this->commonResponse('error', 'Article not found!', Response::HTTP_NOT_FOUND);
        }
        try {
            $article->delete();
            return $this->commonResponse('Success', 'Record deleted!', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->commonResponse('error', 'Could not delete article,please try again!', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
