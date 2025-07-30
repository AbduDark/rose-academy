<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Student submits a Vodafone Cash payment request.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id'          => 'required|exists:courses,id',
            'transaction_number' => 'required|string|unique:payments,transaction_number',
            'phone_number'       => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = Payment::create([
            'user_id'            => $request->user()->id,
            'course_id'          => $request->course_id,
            'transaction_number' => $request->transaction_number,
            'phone_number'       => $request->phone_number,
            'status'             => 'pending',
        ]);

        return response()->json(['message' => 'Payment request submitted', 'data' => $payment], 201);
    }

    /**
     * Admin: list all pending payments
     */
    public function pending()
    {
        $payments = Payment::where('status', 'pending')
            ->with(['user', 'course'])
            ->get();

        return response()->json($payments);
    }

    /**
     * Admin accepts a payment, creates subscription
     */
    public function accept($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json(['message' => 'Payment already processed'], 409);
        }

        // Mark payment accepted
        $payment->status = 'accepted';
        $payment->save();

        // Create subscription for user
        Subscription::create([
            'user_id'     => $payment->user_id,
            'course_id'   => $payment->course_id,
            'start_date'  => now(),
            'end_date'    => now()->addMonth(),
            'status'      => 'active',
        ]);

        return response()->json(['message' => 'Payment accepted and subscription created']);
    }

    /**
     * Admin rejects a payment
     */
    public function reject($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json(['message' => 'Payment already processed'], 409);
        }

        $payment->status = 'rejected';
        $payment->save();

        return response()->json(['message' => 'Payment rejected']);
    }
}
