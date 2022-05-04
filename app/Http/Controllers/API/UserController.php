<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\CheckerTrait;

class UserController extends Controller
{
    // User Controller

    use CheckerTrait;

    // Update User
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'required|string|min:6',
        ]);

        // Send failed response if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Request is valid, update the user
        $user->update([
            'firstName' => $request->get('firstName'),
            'lastName' => $request->get('lastName'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);

        $this->create_request($user->id, 'Update');

        // Return a successful response
        return response()->json([
            'success' => true,
            'message' => 'User will be updated when admin approves',
            'data' => $user,
            'others' => request()->all()
        ], Response::HTTP_OK);
    }

    // Delete User
    public function delete($id)
    {
        $user = User::findOrFail($id);

        $this->create_request($user->id, 'Delete');

        // $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User will be deleted when admin approves',
            'data' => $user,
            'others' => request()->all()
        ], Response::HTTP_OK);
    }
}
