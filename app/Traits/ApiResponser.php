<?php


namespace App\Traits;


use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    /**
     * Build  response
     * //     *
     * @param array|string|mixed|null $message
     * @param array|string|mixed|null $result
     * @param int $code
     * @return JsonResponse
     */
    public function commonResponse($message, $result, int $code): JsonResponse
    {
        return response()->json(['message' => $message, 'result' => $result], $code);
    }


}
