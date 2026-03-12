<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use App\Models\Internship;
use App\Http\Requests\Application\StoreApplicationReq;
use Illuminate\Support\Facades\DB;


class applicationController extends Controller
{
    public function index(Application $app){
       return $app->all();
    }


    public function store(StoreApplicationReq $request){

    $fields = $request->validated();

    return DB::transaction(function () use ($fields, $request) {

        
        $user = User::find($request->user_id);

        if (!$user) {
            return abort(404 ,['error' => 'User not found']);
        }

        $internship = Internship::find($request->internship_id);
        
        if(!$internship){
            return abort(404 ,['error' => 'Internship not found']);
        }

        if($user->role->role !== "student"){
            return abort(403,['error' => 'You dont have permisson']);
        }


        return $fields;

        });


    }


}   
