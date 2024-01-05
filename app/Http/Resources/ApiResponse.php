<?php

namespace App\Http\Resources;

// use Illuminate\Http\Resources\Json\JsonResource;

use stdClass;
class ApiResponse 
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function apiJsonResponse($code, $message, $data, $error)
    {
        $response = new stdClass();
        $response->status_code = $code;
        $response->message = $message;
        $response->error = $error;
        $response->data = $data;
        return response()->json($response, $response->status_code);
    }
}
