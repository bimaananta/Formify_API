<?php

namespace App\Http\Controllers;

use App\Models\AllowedDomains;
use App\Models\Forms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FormsController extends Controller
{
    public function index()
    {
        $creator_id = Auth::user()->id;

        $forms = Forms::where('creator_id', $creator_id)->get();

        if(is_null($forms->first())){
            return response()->json([
                "message" => "Forms not found"
            ], 404);
        }

        return $this->createResponseAPI(200, "Get all forms success","forms", $forms);

    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "name" => "required",
            "slug" => "required|unique:forms",
            "description" => "required",
            "limit_one_response" => "required|boolean",
            "allowed_domains" => "array"
        ]);

        if($validation->fails()){
            return $this->createResponseValidate(($validation->errors()));
        }

        $creator_id = Auth::user()->id;

        $form = Forms::create(["name" => $request->name, "slug" => $request->slug, "description" => $request->description, "creator_id" => $creator_id, 'limit_one_response' => $request->limit_one_response]);

        foreach($request->allowed_domains as $domain){
            AllowedDomains::create(["form_id" => $form->id, "domain" => $domain]);
        }

        return $this->createResponseAPI(200, "Create form success", "form", $form);
    }

    public function show($slug)
    {
        $form = Forms::with(['allowed_domains' => 
            function($domain){
                $domain->get(['domain']);
        }, 'questions'])->where('slug', $slug)->first();

        $formDetail = $form;
        $allowed_domains = [];
    
        foreach($formDetail["allowed_domains"] as $domain){
            $allowed_domains[] = $domain->domain;
        }

        for($i = 0; $i < count($allowed_domains); $i++){
            $formDetail["allowed_domains"][$i] = $allowed_domains[$i];
        }

        return $this->createResponseAPI(200, "Get form success", "form", $formDetail);
    }


}
