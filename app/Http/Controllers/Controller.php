<?php

/**
 * This file is part of Store Management project.
 *
 * (c) Maryam Talebi <mym.talebi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file readme.md.
 */

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Returns successful request result.
     *
     * @param mixed $data
     * @param int   $code
     *
     * @return JsonResponse
     */
    public function success($data, int $code = 200): JsonResponse
    {
        return response()->json(['data' => $data], $code);
    }

    /**
     * Returns failed request error.
     *
     * @param mixed $error
     * @param int   $code
     *
     * @return JsonResponse
     */
    public function error($error, int $code): JsonResponse
    {
        return response()->json(['error' => $error], $code);
    }
}
