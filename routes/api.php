<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| Routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group. Enjoy building!
|
*/

// Public (Guest) Routes
Route::prefix('auth')->group(function () {
    Route::post('register',        [AuthController::class, 'register']);
    Route::post('login',           [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('verify-email',    [AuthController::class, 'verifyEmail']);
    Route::post('reset-password',  [AuthController::class, 'resetPassword']);
});

// Guest-accessible courses
Route::get('courses',       [CourseController::class, 'index']);
Route::get('courses/{id}',  [CourseController::class, 'show']);
Route::get('courses/{id}/ratings', [RatingController::class, 'index']);

// Authenticated User Routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::put('update',    [AuthController::class, 'updateProfile']);
        Route::put('password',  [AuthController::class, 'changePassword']);
    });

    // Subscriptions
    Route::get('my-subscriptions',        [SubscriptionController::class, 'mySubscriptions']);
    Route::post('subscribe',              [SubscriptionController::class, 'subscribe']);
    Route::delete('unsubscribe/{course_id}', [SubscriptionController::class, 'unsubscribe']);

    // Favorites
    Route::post('favorite/{course_id}',   [FavoriteController::class, 'add']);
    Route::delete('favorite/{course_id}', [FavoriteController::class, 'remove']);

    // Lessons & Comments
    Route::get('courses/{id}/lessons',         [LessonController::class, 'index']);
    Route::post('comments',                    [CommentController::class, 'store']);
    Route::get('lessons/{lesson_id}/comments', [CommentController::class, 'index']);

    // Ratings & Payments
    Route::post('ratings',                     [RatingController::class, 'store']);
    Route::post('payments/vodafone',           [PaymentController::class, 'store']);
});

// Admin-only Routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Courses Management
    Route::post('courses',   [CourseController::class, 'store']);
    Route::put('courses/{id}',    [CourseController::class, 'update']);
    Route::delete('courses/{id}', [CourseController::class, 'destroy']);

    // Lessons Management
    Route::post('lessons',           [LessonController::class, 'store']);
    Route::put('lessons/{id}',       [LessonController::class, 'update']);
    Route::delete('lessons/{id}',    [LessonController::class, 'destroy']);

    // Payments Approval
    Route::get('admin/payments/pending',          [PaymentController::class, 'pending']);
    Route::post('admin/payments/accept/{id}',     [PaymentController::class, 'accept']);
    Route::post('admin/payments/reject/{id}',     [PaymentController::class, 'reject']);

    // Users Management
    Route::get('admin/users',        [UserController::class, 'index']);
    Route::put('admin/users/{id}',   [UserController::class, 'update']);
    Route::delete('admin/users/{id}',[UserController::class, 'destroy']);
});
