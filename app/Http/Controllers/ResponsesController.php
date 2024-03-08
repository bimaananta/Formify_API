<?php

namespace App\Http\Controllers;

use App\Models\Answers;
use App\Models\Forms;
use App\Models\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ResponsesController extends Controller
{
    public function index($form_slug)
    {
        $user = Auth::user();
        $form = Forms::where('slug', $form_slug)->first();

        if(is_null($form)){
            return response()->json([
                "message" => "Form not found"
            ], 404);    
        }

        if($user->id != $form->creator_id){
            return $this->forbiddenAccess();
        }

        $responses = Responses::with(['user', 'answers' => function($answers){
            $answers->with('questions');
        }])->where('form_id', $form->id)->get();

        $responsesData = $responses;
        $index = 0;

        foreach($responsesData as $response){
            $answers = null;

            foreach($response->answers as $answer){
                $question_name = $answer->questions->name;
                $answers["$question_name"] = $answer->value;
            }

            unset($responsesData[$index]["answers"]);
            $responsesData[$index]["answers"] = $answers;

            $index++;
        }

        return $this->createResponseAPI(200, "Get responses success", "responses", $responsesData);
    
    }


    public function store(Request $request, $form_slug)
    {
        $form = Forms::with('allowed_domains')->where('slug', $form_slug)->first();
        $user = Auth::user();

        if(is_null($form)){
            return response()->json([
                "message" => "Form not founf"
            ], 404);
        }

        $user_response = Responses::where('user_id', $user->id)->where('form_id', $form->id)->first();

        if(!is_null($user_response)){
            return response()->json([
                "message" => "You can not submit form twice"
            ], 403);
        }

        $formData = $form;
        $allowed_domains = [];

        foreach($formData["allowed_domains"] as $domain){
            $allowed_domains[] = $domain->domain;
        }

        $user_domain = explode('@', $user->email)[1];

        if(!in_array($user_domain, $allowed_domains)){
            return $this->forbiddenAccess();
        }

        $validation = Validator::make($request->all(), [
            "answers" => "required|array",
        ]);

        if($validation->fails()){
            return $this->createResponseValidate($validation->errors());
        }

        $response = new Responses();
        $response->form_id = $form->id;
        $response->user_id = $user->id;
        $response->date = now();

        try{
            $response->save();
        }catch(\Exception $e){
            return response()->json([
                "message" => "Failed to create response ". $e->getMessage()
            ], 400);
        }

        foreach($request->answers as $answerData){
            $answer = new Answers();
            $answer->responses_id = $response->id;
            $answer->questions_id = $answerData["question_id"];
            $answer->value = $answerData["value"];

            try{
                $answer->save();
            }catch(\Exception $e){
                return response()->json([
                    "message" => "Failed save answer",
                ], 400);
            }
        }

        return response()->json([
            "message" => "Submit response success"
        ], 200);
    }
}
