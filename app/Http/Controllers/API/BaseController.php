<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller 
{
    public function sendResponce($result,$message){
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message
        ];
        return response()->json($response,200);
    }

    public function sendError($error,$errorMessage=[],$code=404){
        $response = [
            'success' => false,
            'message' => $message
        ];

        if(!empty($errorMessage)){
            $response['data'] = $errorMessage;
        }

        return response()->json($response,$code);
    }

}
