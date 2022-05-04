<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Checker;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Traits\CheckerTrait;
use App\Models\User;

class CheckerController extends Controller
{
    use CheckerTrait;

    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_pending_request()
    {

        $is_requested = $this->validate_by_user_id(JWTAuth::user()->id);
        if (!$is_requested) {
            return response()->json([
                'success' => false,
                'message' => 'Admin has not approved your account yet',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $checker = Checker::select('id','user_id','request_type')->whereNotIn('user_id', [JWTAuth::user()->id])->get();
        return response()->json([
            'success' => true,
            'message' => 'Pending request',
            'data' => $checker
        ], Response::HTTP_OK);

    }

    public function accept_request($user_id)
    {
        // Check if pending request is valid
        $is_requested = $this->validate_by_user_id($user_id);
        if (!$is_requested) {
            return response()->json([
                'success' => false,
                'message' => 'This request is not valid',
            ], Response::HTTP_BAD_REQUEST);
        }

        $checker = Checker::where('user_id', $user_id)->first();

        if ($checker->request_type == 'Delete') {
            $user = User::findOrFail($checker->user_id);
            $user->delete();
            $checker->delete();

            return response()->json([
                'success' => true,
                'message' => 'User was deleted',
                'others' => request()->all()
            ], Response::HTTP_OK);
        } else {
            $checker->delete();

            return response()->json([
                'success' => true,
                'message' => 'User request has been accepted',
                'others' => request()->all()
            ], Response::HTTP_OK);
        }
    }

    public function decline_request($user_id)
    {
        // Check if pending request is valid
        $is_requested = $this->validate_by_user_id($user_id);
        if (!$is_requested) {
            return response()->json([
                'success' => false,
                'message' => 'This request is not valid',
            ], Response::HTTP_BAD_REQUEST);
        }

        $checker = Checker::where('user_id', $user_id)->first();
        $checker->delete();

        return response()->json([
            'success' => true,
            'message' => 'User request has been declined',
            'others' => request()->all()
        ], Response::HTTP_OK);
    }
}
