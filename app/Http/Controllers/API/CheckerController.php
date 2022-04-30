<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Checker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Traits\CheckerTrait;

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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Checker  $checker
     * @return \Illuminate\Http\Response
     */
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Checker  $checker
     * @return \Illuminate\Http\Response
     */
    public function decline_request(Checker $checker)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Checker  $checker
     * @return \Illuminate\Http\Response
     */
    public function destroy(Checker $checker)
    {
        //
    }
}
