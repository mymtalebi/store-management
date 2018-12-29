<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    /**
     * Returns successful request result.
     *
     * @param  mixed $data
     * @param  int $code
     * @return JsonResponse
     */
    public function success($data, int $code = 200): JsonResponse
    {
        return response()->json(['data' => $data], $code);
    }

    /**
     * Returns failed request error.
     *
     * @param  mixed $error
     * @param  int $code
     * @return JsonResponse
     */
    public function error($error, int $code): JsonResponse
    {
        return response()->json(['error' => $error], $code);
    }
}
