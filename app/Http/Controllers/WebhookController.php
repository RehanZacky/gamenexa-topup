<?php

namespace App\Http\Controllers;

use App\Services\DigiflazzService;
use App\Services\DoitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Endpoint Webhook Callback Doit.id
     * POST /webhook/duitku
     */
    public function doit(Request $request, DoitService $doit): JsonResponse
    {
        $payload = $request->all();
        if (!$doit->verifySignature($request->getContent(), $request->header('PayBridge-Signature'))) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        if (!in_array($payload['type'] ?? null, ['payment.paid', 'payment.expired'], true)) {
            return response()->json(['status' => 'ignored']);
        }

        $payment = $doit->handleWebhook($payload);

        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }

        return response()->json(['status' => 'success']);
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
