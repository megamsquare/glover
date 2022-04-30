<?php
namespace App\Http\Controllers\Traits;

use App\Models\Checker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait CheckerTrait
 */
trait CheckerTrait
{
    // Create Checker
    public function create_request($user_id, $request_type)
    {

        // Request is valid, create the checker
        $checker = Checker::create([
            'user_id' => $user_id,
            'request_type' => $request_type,
        ]);
    }

    public function validate_by_user_id($user_id)
    {
        // Check if user has a pending request
        $checker = Checker::where('user_id', $user_id)->first();

        // If user has a pending request, return false
        if ($checker) {
            return false;
        }

        // If user does not have a pending request, return true
        return true;
    }
}

