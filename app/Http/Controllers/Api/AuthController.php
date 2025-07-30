<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendPinMail;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'gender'   => 'required|in:male,female',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'gender'   => $request->gender,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'token'   => $user->createToken('auth_token')->plainTextToken
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! Hash::check($request->password, (string) $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'messeage' => 'Login Done'
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed'
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password incorrect'], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profiles', 'public');
            $user->image = $path;
        }

        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $pin = rand(100000, 999999);

        EmailVerification::updateOrCreate(
            ['email' => $request->email],
            ['pin' => $pin, 'expires_at' => now()->addMinutes(15)]
        );

        // TODO: Send the pin via email
        Mail::to($request->email)->send(new SendPinMail($pin));


        return response()->json(['message' => 'Verification PIN sent to email']);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'pin'   => 'required|string'
        ]);

        $verification = EmailVerification::where('email', $request->email)
            ->where('pin', $request->pin)
            ->where('expires_at', '>', now())
            ->first();

        if (! $verification) {
            return response()->json(['message' => 'Invalid or expired PIN'], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();

        $verification->delete();

        return response()->json(['message' => 'Email verified successfully']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'pin'      => 'required|string',
            'password' => 'required|min:6|confirmed'
        ]);

        $verification = EmailVerification::where('email', $request->email)
            ->where('pin', $request->pin)
            ->where('expires_at', '>', now())
            ->first();

        if (! $verification) {
            return response()->json(['message' => 'Invalid or expired PIN'], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        $verification->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
