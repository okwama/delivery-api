<?php

namespace App\Http\Controllers;

use App\Mail\ContactFeedback;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function contact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $contact = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'subject' => $request->subject,
                    'message' => $request->message,
                ];
                Mail::to(config('mail.from.to'))->send(new ContactFeedback($contact));
                return $this->commonResponse('success', 'Email Sent Successfully!', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            }
        }
    }
}
