<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
    return User::all();
}

public function update(Request $request, $id) {
    $user = User::findOrFail($id);
    $user->update($request->only('name', 'email', 'is_admin'));
    return $user;
}

public function destroy($id) {
    User::findOrFail($id)->delete();
    return response()->json(['message' => 'User deleted']);
}

}
