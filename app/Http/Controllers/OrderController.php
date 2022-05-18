<?php

namespace App\Http\Controllers;

use Exception;
use App\Jobs\SendEmail;
use App\Mail\ClientOrder;
use App\Mail\OrderReceived;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Swift_TransportException;
use App\Helpers\GeneralHelper;
use App\Models\Products\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * all orders
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $q = $request->search_query;
        $status = $request->status_filter;
        $orders = Order::query();
        // filter by search term
        if ($request->has('search_query') && $request->filled('search_query')) {
            $orders->where('orderNo', 'like', '%' . $q . '%')
                ->orWhere('name', 'like', '%' . $q . '%')
                ->orWhere('email', 'like', '%' . $q . '%')
                ->orWhere('phone', 'like', '%' . $q . '%');
        }
        if ($request->has('status_filter') && $request->filled('status_filter')) {
            if ($status === 'pending') {
                $orders->where('pending', true);
            }
            if ($status === 'closed') {
                $orders->where('pending', false);
            }
        }
        $orders = $orders->orderBy('created_at', 'desc')->paginate(10);
        return $this->commonResponse('Success', $orders, Response::HTTP_OK);
    }

    /**
     * pending orders
     * @param Request $request
     * @return JsonResponse
     */
    public function pendingOrders(Request $request): JsonResponse
    {
        $q = $request->search_query;
        $orders = Order::query();
        // filter by search term
        if ($request->has('search_query') && $request->filled('search_query')) {
            $orders->where('orderNo', 'like', '%' . $q . '%')
                ->orWhere('name', 'like', '%' . $q . '%')
                ->orWhere('email', 'like', '%' . $q . '%')
                ->orWhere('phone', 'like', '%' . $q . '%');
        }
        $orders = $orders->where('pending', true)->orderBy('created_at', 'desc')
            ->paginate(10);
        return $this->commonResponse('Success', $orders, Response::HTTP_OK);
    }

    /**
     * completed orders
     * @param Request $request
     * @return JsonResponse
     */
    public function completedOrders(Request $request): JsonResponse
    {
        $q = $request->search_query;
        $orders = Order::query();
        // filter by search term
        if ($request->has('search_query') && $request->filled('search_query')) {
            $orders->where('orderNo', 'like', '%' . $q . '%')
                ->orWhere('name', 'like', '%' . $q . '%')
                ->orWhere('email', 'like', '%' . $q . '%')
                ->orWhere('phone', 'like', '%' . $q . '%');
        }
        $orders = $orders->where('pending', false)->orderBy('created_at', 'desc')
            ->paginate(10);
        return $this->commonResponse('Success', $orders, Response::HTTP_OK);
    }

    public function closeOrder($id): JsonResponse
    {
        $order = Order::where([['_id', $id], ['pending', true]])->first();
        if (is_null($order) || !isset($order)) {
            return $this->commonResponse('error', 'Order not found!', Response::HTTP_NOT_FOUND);
        }
        try {
            $order->update(['pending' => false]);
            return $this->commonResponse('success', 'Order Closed successfully!', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->commonResponse('error', 'Could not delete meta,please try again!', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *display user details
     * @return JsonResponse
     */
    public function myOrders(): JsonResponse
    {
        $user = Auth::user();
        $orders = Order::where('phone', $user->phone)
                ->orWhere('email', $user->email)
                ->get() ?? [];
        return $this->commonResponse('success', $orders, Response::HTTP_OK);
    }

    /**
     *display user details
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $order = Order::find($id);
        if (is_null($order) || !isset($order)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        return $this->commonResponse('success', $order, Response::HTTP_OK);
    }

    /**
     * Place order
     * @param Request $request
     * @return JsonResponse
     */
    public function order(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'instructions' => 'nullable',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'location' => 'required',
            'road' => 'nullable',
            'house' => 'nullable',
            'street' => 'nullable',
            'building' => 'nullable',
            'amountPaid' => 'nullable|integer',
            'discountApplied' => 'nullable|integer',
            'paymentOption' => 'required',
            'total' => 'nullable|integer',
            'products' => 'required|array',
            'deliveryDate' => 'nullable',
            'pending' => 'nullable|boolean',
            'rejected' => 'nullable|boolean',
            'handled' => 'nullable|boolean',
            'approved' => 'nullable|boolean',
            'confirmed' => 'nullable|boolean',
            'paid' => 'nullable|boolean',
            'scheduled' => 'nullable|boolean',
            'scheduleDate' => 'nullable|date',
            'shipped' => 'nullable|boolean',
            'dateShipped' => 'nullable|date',
            'orderCategory' => 'nullable',
            'medium' => 'nullable',
            'reason' => 'nullable',
        ]);
        if ($data->fails()) {
            return $this->commonResponse('error', Arr::flatten($data->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $orderNo = GeneralHelper::generateRandomInteger(5);
                $inputs = $data->validated();

                $inputs['phone'] = preg_replace('/\s+/', '', $inputs['phone']);
                $inputs['orderNo'] = $orderNo;
                $inputs['placedOn'] = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
//                $phone=$inputs['phone'];
                $order = Order::create($inputs);
                // Send SMS
                //                $message="Dear Carson, This is a test sms";
                //                $this->SMS()->send(
                //                    [
                //                        'to' => $phone,
                //                        'message' => $message,
                //                        'from' => $this->sendId()
                //                    ]
                //                );
                $email_order = [
                    'customer_name' => $order->name,
                    'customer_email' => $order->email,
                    'location' => $order->location,
                    'orderNo' => $order->orderNo,
                    'phone' => $order->phone,
                ];
                // Send emails without Queuing
             
                Mail::to(config('mail.from.address'))
            ->cc(config('mail.from.cc'))
            ->send(new OrderReceived($email_order));
                // send to client
                Mail::to($email_order['customer_email'])
            ->send(new ClientOrder($email_order));

                //  SendEmail::dispatch($email_order);
                return $this->commonResponse('Success', 'Order placed successfully!', Response::HTTP_CREATED);
            } catch (Swift_TransportException $e) {
                return $this->commonResponse('Error', $e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
            } catch (QueryException $ex) {
                return $this->commonResponse('Error', $ex->errorInfo[2], Response::HTTP_EXPECTATION_FAILED);
            } catch (Exception $ex) {
                return $this->commonResponse('Error', $ex->getMessage(), Response::HTTP_EXPECTATION_FAILED);
            }
        }
    }

    /**
     * Update order
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'amountPaid' => 'nullable|integer',
            'discountApplied' => 'nullable|integer',
            'deliveryDate' => 'nullable|date',
            'pending' => 'nullable|boolean',
            'rejected' => 'nullable|boolean',
            'handled' => 'nullable|boolean',
            'approved' => 'nullable|boolean',
            'confirmed' => 'nullable|boolean',
            'paid' => 'nullable|boolean',
            'scheduled' => 'nullable|boolean',
            'scheduleDate' => 'nullable|date',
        ]);
        if ($data->fails()) {
            return $this->commonResponse('error', Arr::flatten($data->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            try {
                $inputs = $data->validated();
                $order = Order::where('_id', $id)->first();
                $order->update($inputs);
                return $this->commonResponse('success', 'Order updated successfully!', Response::HTTP_CREATED);
            } catch (QueryException $ex) {
                return $this->commonResponse('error', $ex->errorInfo[2], Response::HTTP_UNPROCESSABLE_ENTITY);
            } catch (Exception $ex) {
                return $this->commonResponse('error', $ex->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }
}
