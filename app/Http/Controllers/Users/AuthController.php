<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Authenticate user
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function loginUser(Request $request): JsonResponse
    {
        $validatedLogin = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );
        if ($validatedLogin->fails()) {
            return $this->commonResponse(Arr::flatten($validatedLogin->messages()->get('*')), '', Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $email = $validatedLogin->validated()['email'];
            $password = $validatedLogin->validated()['password'];
            $user = User::where('email', $email)->first();
            if (is_null($user)) {
                return $this->commonResponse('The provided credentials are incorrect!', 'The provided credentials are incorrect!', Response::HTTP_NOT_FOUND);
            } elseif (!Hash::check($password, $user->password)) {
                return $this->commonResponse('The provided credentials are incorrect.', 'The provided credentials are incorrect.', Response::HTTP_UNAUTHORIZED);
            }
            $result = [
                'user' => new UserResource($user),
                'accessToken' => $user->createToken('NAIROBI DRINKS')->accessToken,
            ];
            return $this->commonResponse('success', $result, Response::HTTP_CREATED);
        }
    }

    /**
     * Authenticated user profile
     * @return JsonResponse
     */
    public function profile()
    {
        $user = Auth::user();
        return $this->commonResponse('Success', new UserResource($user), Response::HTTP_OK);
    }

    /**
     * Change Password
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'currentPassword' => 'required',
                'newPassword' => 'required|min:6',
            ]
        );
        if ($validator->fails()) {
            return $this->commonResponse(Arr::flatten($validator->messages()->get('*')), '', Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            if (!(Hash::check($request->get('currentPassword'), Auth::user()->password))) {
                // The passwords matches
                return $this->commonResponse('error', 'Your current password does not match with the password you provided. Please try again.!', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            if (strcmp($request->get('currentPassword'), $request->get('newPassword')) == 0) {
                //Current password and new password are same
                return $this->commonResponse('error', 'New Password cannot be same as your current password. Please choose a different password!', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            //Change Password
            $user = Auth::user();
            $user->password = bcrypt($request->get('newPassword'));
            $user->save();
            return $this->commonResponse('success', 'Password has been changed', Response::HTTP_CREATED);
        }
    }
}
