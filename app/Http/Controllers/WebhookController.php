<?php

namespace App\Http\Controllers;

use App\Services\DigiflazzService;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Endpoint Webhook Callback Midtrans
     * POST /webhook/midtrans
     */
    public function midtrans(Request $request, MidtransService $midtrans): JsonResponse
    {
        $payload = $request->all();
        Log::info('Received Midtrans Webhook Notification', ['payload' => $payload]);

        $payment = $midtrans->handleNotification($payload);

        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Payment not found or invalid signature'], 400);
        }

        return response()->json(['status' => 'success', 'message' => 'Payment notification processed']);
    }

    /**
     * Endpoint Webhook Callback Digiflazz
     * POST /webhook/digiflazz
     */
    public function digiflazz(Request $request, DigiflazzService $digiflazz): JsonResponse
    {
        $payload = $request->all();
        Log::info('Received Digiflazz Webhook Notification', ['payload' => $payload]);

        $transaction = $digiflazz->handleWebhook($payload);

        return response()->json([
            'status' => 'success',
            'message' => 'Digiflazz callback processed',
            'ref_id' => $transaction?->ref_id,
        ]);
    }
}
