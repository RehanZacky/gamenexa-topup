<?php

namespace App\Http\Controllers;

use App\Services\AccountValidatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountValidationController extends Controller
{
    /**
     * Endpoint API AJAX untuk Validasi Akun Game / No HP / PLN
     */
    public function check(Request $request, AccountValidatorService $validator): JsonResponse
    {
        $categorySlug = $request->input('category_slug', '');
        $customerNumber = $request->input('customer_number', '');
        $zoneId = $request->input('zone_id');

        $result = $validator->validate($categorySlug, $customerNumber, $zoneId);

        return response()->json($result);
    }
}
