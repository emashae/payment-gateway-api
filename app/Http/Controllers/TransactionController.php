<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CardValidationService;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Ramsey\Uuid\Guid\Guid;
use Illuminate\Support\Facades\Hash;

class TransactionController extends Controller
{
    protected $cardValidationService;

    public function __construct(CardValidationService $cardValidationService)
    {
        $this->cardValidationService = $cardValidationService;
    }

    /**
     * Create a new transaction.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTransaction(Request $request)
    {
        // Validate 
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string|size:16',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'customer_email' => 'required|email',
            'metadata' => 'nullable|array',
            'transaction_time' => 'nullable|date_format:Y-m-d H:i:s'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        // Mask the card number
        $maskedCardNumber = $this->maskCardNumber($data['card_number']);

        $status = $this->cardValidationService->validateTransaction(
            $data['card_number'],
            $data['amount'],
            $data['currency'],
            $data['metadata'] ?? [],
            $data['transaction_time'] ?? now(),
            $data['customer_email']
        );

        if ($status) {
            $transaction = Transaction::create([
                'id' => (string)Guid::uuid4(),
                'masked_card_number' => $maskedCardNumber,  
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'customer_email' => $data['customer_email'],
                'status' => $status,
                'metadata' => $data['metadata'] ?? [],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json($transaction, Response::HTTP_CREATED);
        }

        return response()->json([
            'message' => 'Transaction declined.',
            'status' => $status
        ], Response::HTTP_OK);
    }

    /**
     * Mask the card number
     * @param string $cardNumber
     * @return string
     */
    private function maskCardNumber(string $cardNumber): string
    {
        return substr($cardNumber, 0, 6) . str_repeat('*', 6) . substr($cardNumber, -4);
    }

}