<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * show all users, this is an admin function
     * @param Request $request
     * @return JsonResponse
     */
    public function users(Request $request): JsonResponse
    {
        $q = $request->search_query;
        $users = User::query();
        if ($request->has('search_query') && $request->filled('search_query')) {
            $users->where('name', 'like', '%' . $q . '%')
                ->orWhere('role', 'like', '%' . $q . '%')
                ->orWhere('email', 'like', '%' . $q . '%')
                ->orWhere('phone', 'like', '%' . $q . '%');
        }
        $users = $users->where('role', '<>', 'admin')->get()
            ->transform(function ($item) {
                return new UserResource($item);
            })->paginate(10);
        return $this->commonResponse('success', $users, Response::HTTP_OK);

    }

    /**
     *display user details
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);
        if (is_null($user) || !isset($user)) {
            return $this->commonResponse('error', 'User not found!', Response::HTTP_NOT_FOUND);
        }
        return $this->commonResponse('success', new UserResource($user), Response::HTTP_OK);

    }

    /**
     * Save user to database
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'address' => 'nullable',
            'phone' => 'required',
            'location' => 'nullable',
            'role' => 'nullable',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            return $this->commonResponse(Arr::flatten($validator->messages()->get('*')), Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $userCredentials = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'location' => $request->location,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'role' => $request->role ?? 'user',
                    'password' => bcrypt($request->password),
                ];
                User::create($userCredentials);
                return $this->commonResponse('success', 'User registration successful', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse($ex->errorInfo[2], $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse($ex->getMessage(), $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }
    }

    /**
     * Update user details
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'nullable',
            'phone' => 'required',
            'location' => 'nullable',
            'role' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $user = User::find($id);
                if (is_null($user) || !isset($user)) {
                    return $this->commonResponse('error', [], Response::HTTP_NOT_FOUND);
                }
                $userCredentials = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'location' => $request->location,
                    'address' => $request->address,
                    'role' => $request->role,
                ];
                $user->update($userCredentials);
                return $this->commonResponse('success', 'User details updated successfully', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }
    }

    /**
     * Delete user
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        $user = User::find($id);
        if (is_null($user) || !isset($user)) {
            return $this->commonResponse('error', 'User not found!', Response::HTTP_NOT_FOUND);
        }
        try {
            $user->delete();
            return $this->commonResponse('Success', 'Record deleted!', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->commonResponse('Error', 'Could not delete user', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
