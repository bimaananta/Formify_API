<?php

namespace App\Http\Controllers;

use App\Models\Forms;
use App\Models\Questions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionsController extends Controller
{
    public function store(Request $request, $form_slug)
    {
        $user_id = Auth::user()->id;
        $form = Forms::where('slug', $form_slug)->first();

        if($form->creator_id != $user_id){
            return response()->json([
                "message" => "Forbidden access"
            ], 403);
        }

        $validation = Validator::make($request->all(), [
            "name" => "required",
            "choice_type" => "required|in:short answer,paragraph,date,multiple choice,dropdown,checkboxes",
            "choices" => ($request->choice_type == "dropdown" || $request->choice_type == "checkboxes" || $request->choice_type == "multiple_choices") ? "required|array|string" : "nullable",
            "is_required" => "required|boolean"
        ]);

        if($validation->fails()){
            return $this->createResponseValidate($validation->errors());
        }

        $choice_type = explode( ' ', $request->choice_type);
        $choice_type = implode('_', $choice_type);

        $question = Questions::create(["form_id" => $form->id, "name" => $request->name, "choice_type" => $choice_type, "is_required" => $request->is_required]);

        if($request->choice_type == "dropdown" || $request->choice_type == "multiple choice" || $request->choice_type == "checkboxes"){
            $joined_choices = implode(',', $request->choices);
            $question->update(["choices" => $joined_choices]);
        }

        return $this->createResponseAPI(200, "Create question success", "question", $question);

    }

    public function destroy($form_slug ,$id)
    {   
        $user_id = Auth::user()->id;
        $form = Forms::where('slug', $form_slug)->first();

        if(is_null($form)){
            return response()->json([
                "message" => "Form not found"
            ], 404);
        }

        if($form->creator_id != $user_id){
            return response()->json([
                "message" => "Forbidden access"
            ], 403);
        }

        $question = Questions::findOrFail($id);

        if(is_null($question)){
            return response()->json([
                "message" => "Question not found"
            ], 404);
        }

        try{
            $question->delete();
        }catch(\Exception $e){
            return response()->json([
                "message" => "Failed to delete question ". $e->getMessage()
            ], 400);
        }

        return response()->json([
            "message" => "Remove question success"
        ], 200);
    }
}
