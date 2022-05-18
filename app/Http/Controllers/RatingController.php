<?php

namespace App\Http\Controllers;

use App\Http\Resources\RatingResource;
use App\Models\Rating;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RatingController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $q = $request->search_query;
//        $date_filter = $request->date_filter;
//        // date settings
//        $today = Carbon::today()->format('Y-m-d');
//        $yesterday = Carbon::yesterday()->format('Y-m-d');
//        $this_month = Carbon::today()->format('m');
//        $last_month = Carbon::now()->subMonth()->format('m');
//        $this_year = Carbon::now()->format('Y');
//        $week_of_the_year = Carbon::now()->weekOfYear;
//        $date = Carbon::now(); // or $date = new Carbon();
//
//        $date->setISODate($this_year, $week_of_the_year);
//        $first_week_day = Carbon::parse($date->copy()->startOfWeek())->format('Y-m-d');
//        $last_week_day = Carbon::parse($date->copy()->endOfWeek())->format('Y-m-d');

        $rating = Rating::query();
        // filter by search term
        if ($request->has('search_query') && $request->filled('search_query')) {
            $rating->where('name', 'like', '%' . $q . '%')
                ->orWhere('email', 'like', '%' . $q . '%')
                ->orWhere('product.name', 'like', '%' . $q . '%')
                ->orWhere('phone', 'like', '%' . $q . '%');
        }
//        if ($date_filter === 'today') {
//            $rating->where('created_at', $today);
//        }
//        if ($date_filter === 'yesterday') {
//            $rating->where('created_at', $yesterday);
//        }
//        if ($date_filter === 'this_month') {
//            $rating->where('created_at', $this_month);
//        }
//        if ($date_filter === 'last_month') {
//            $rating->where('created_at', $last_month);
//        }
//        if ($date_filter === 'this_year') {
//            $rating->where('created_at', $this_year);
//        }
//        if ($date_filter === 'this_week') {
//            $rating->whereBetween('created_at', [$first_week_day, $last_week_day]);
//        }
        $rating = $rating->orderBy('created_at', 'desc')
            ->get()->transform(function ($item) {
                return new RatingResource($item);
            })->paginate(10);
        return $this->commonResponse('success', $rating, Response::HTTP_OK);
    }

    /**
     * get client total reviews
     * @return JsonResponse
     */
    public function clientReviews(): JsonResponse
    {
        $user = Auth::user();
        $reviews = Rating::where(function ($query) use ($user) {
            $query->where('email', $user->email);
        })->get();
        return $this->commonResponse('success', $reviews, Response::HTTP_OK);
    }

    /**
     * get client product review
     * @param $productId
     * @return JsonResponse
     */
    public function clientProductReview($productId): JsonResponse
    {
        $user = Auth::user();
        $reviews = Rating::where(function ($query) use ($user) {
            $query->where('email', $user->email);
        })->get();
        $review = $reviews->where('productId', $productId)->first() ?? [];
        return $this->commonResponse('success', $review, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reviews(Request $request): JsonResponse
    {
        $productId = $request->productId;
        $reviews = Rating::where('productId', $productId)
            ->whereStatus(1)->get();
        return $this->commonResponse('success', RatingResource::collection($reviews), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'stars' => 'required|integer',
            'review' => 'required',
            'email' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'productId' => 'required',
            'product' => 'nullable',
            'status' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                Rating::create($validator->validated());
                return $this->commonResponse('success', 'Rating created', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }

    }

    /**
     * Approve review
     * @param $id
     * @return JsonResponse
     */
    public function approve($id): JsonResponse
    {
        $rating = Rating::find($id);
        if (is_null($rating) || !isset($rating)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        $rating->update(['status' => 1]);
        return $this->commonResponse('success', [], Response::HTTP_CREATED);
    }
}
