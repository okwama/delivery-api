<?php

namespace App\Http\Controllers;

use App\Http\Resources\CarouselResource;
use App\Models\Carousel;
use Exception;
use File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CarouselController extends Controller
{
    public function index(): JsonResponse
    {
        $carousel=Carousel::latest()->orderBy('order','asc')->get();
        return $this->commonResponse('Success', CarouselResource::collection($carousel), Response::HTTP_OK);
    }

    /** show carousel
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $carousel = Carousel::find($id);
        if (is_null($carousel) || !isset($carousel)) {
            return $this->commonResponse('error', [], Response::HTTP_OK);
        }
        return $this->commonResponse('success', new CarouselResource($carousel), Response::HTTP_OK);
    }
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'mobile_image' => 'required',
            'caption' => 'nullable',
            'details' => 'nullable',
            'fileName' => 'nullable',
            'mobileFileName' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->commonResponse('error', Arr::flatten($validator->messages()->get('*')), Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            $data = collect($validator->validated());
            //image
            if ($request->has('fileName') && $request->filled('fileName')) {
                $raw_file_name = explode('.', $request->image);
                $extension = end($raw_file_name);
                $uploadedString = $data['fileName'];  // your base64 encoded
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uploadedString));
                $imageName = Str::random(3) . '-' . $raw_file_name[0] . '.' . $extension;
                file_put_contents(public_path() . '/uploads/carousel/' . $imageName, $image);
                $data['image'] = $imageName;
            } else {
                $data['image'] = '';
            }
            //mobile image
            if ($request->has('mobileFileName') && $request->filled('mobileFileName')) {
                $raw_mobile_file_name = explode('.', $request->mobile_image);
                $extension = end($raw_mobile_file_name);
                $uploadedString = $data['mobileFileName'];  // your base64 encoded
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $uploadedString));
                $imageName = Str::random(3) . '-' . $raw_mobile_file_name[0] . '.' . $extension;
                file_put_contents(public_path() . '/uploads/carousel/' . $imageName, $image);
                $data['mobile_image'] = $imageName;
            } else {
                $data['mobile_image'] = '';
            }
            $order=Carousel::query()->orderBy('order','desc')->first();
            if(is_null($order)){
                $order_no=1;
            }
            else{
                $order_no=($order->order)+1;
            }
            $data['order']=$order_no;
            $data->forget('fileName');
            $final_data = $data->toArray();
            Carousel::create($final_data);
            return $this->commonResponse('success', 'carousel image created', Response::HTTP_CREATED);
        }
    }
    public function delete($id): JsonResponse
    {
        $carousel = Carousel::find($id);
        if (is_null($carousel) || !isset($carousel)) {
            return $this->commonResponse('error', 'Carousel not found!', Response::HTTP_NOT_FOUND);
        }
        try {
            if (isset($carousel->photo)) {
                $image_path = public_path('uploads/carousel/' . $carousel->photo);
                if (File::exists($image_path)) {
                    unlink($image_path);
                }
            }
            $carousel->delete();
            return $this->commonResponse('success', 'Carousel deleted!', Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->commonResponse('error', 'Could not delete carousel', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }
}
