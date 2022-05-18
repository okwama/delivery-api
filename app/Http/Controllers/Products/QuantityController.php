<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductQuantity;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class QuantityController extends Controller
{
    /**
     * all quantities
     * @return JsonResponse
     */
    public function allQuantities(): JsonResponse
    {
        $quantities = ProductQuantity::all();
        return $this->commonResponse('Success', $quantities, Response::HTTP_OK);
    }

    /**
     * all quantities paginated
     * @return JsonResponse
     */
    public function quantities(): JsonResponse
    {
        $quantities = ProductQuantity::paginate(10);
        return $this->commonResponse('Success', $quantities, Response::HTTP_OK);
    }
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'units' => 'required',
            'measurement' => 'required',
            'abbreviation' => 'nullable',
            'description' => 'nullable',

        ]);
        if ($validator->fails()) {
            return $this->commonResponse('Error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                ProductQuantity::create($validator->validated());
                return $this->commonResponse('Success', 'Record created successfully', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('Error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('Error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }
    }
    public function show($id): JsonResponse
    {
        $quantity = ProductQuantity::find($id);
        if (is_null($quantity) || !isset($quantity)) {
            return $this->commonResponse('Error', 'Product quantity not found!', Response::HTTP_NOT_FOUND);
        }
        return $this->commonResponse('Success', $quantity, Response::HTTP_OK);
    }
    public function update(Request $request,$id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'units' => 'required',
            'measurement' => 'required',
            'abbreviation' => 'nullable',
            'description' => 'nullable',

        ]);
        if ($validator->fails()) {
            return $this->commonResponse('Error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $quantity = ProductQuantity::find($id);
                if (is_null($quantity) || !isset($quantity)) {
                    return $this->commonResponse('Error', 'Product quantity not found!', Response::HTTP_NOT_FOUND);
                }
                $quantity->update($validator->validated());
                return $this->commonResponse('Success', 'Record updated successfully', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('Error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('Error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }
    }
    /**
     * Delete quantity
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $quantity = ProductQuantity::find($id);
        if (is_null($quantity) || !isset($quantity)) {
            return $this->commonResponse('Error', 'Product quantity not found!', Response::HTTP_NOT_FOUND);
        }
        try {
            $quantity->delete();
            return $this->commonResponse('Success', 'Record deleted!', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->commonResponse('Error', 'Could not delete product quantity', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
