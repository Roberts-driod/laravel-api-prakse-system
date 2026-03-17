<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use App\Models\Internship;
use App\Models\Group;
use App\Models\Role;
use App\Http\Requests\Application\StoreApplicationReq;
use Illuminate\Support\Facades\DB;


class applicationController extends Controller
{
    public function index(Application $app){
       return $app->all();
    }


        public function store(StoreApplicationReq $request)
        {

            $fields = $request->validated();

            return DB::transaction(function () use ($fields, $request) {

                $user = User::find($fields['user_id']);
                if (!$user) abort(404, 'User not found');

                $internship = Internship::find($fields['internship_id']);
                if (!$internship) abort(404, 'Internship not found');

                if ($user->role->role !== "student") {
                    abort(403, 'No permission');
                }

                $exists = Application::where('user_id', $fields['user_id'])
                    ->where('internship_id', $fields['internship_id'])
                    ->exists();

                if ($exists) {
                    abort(409, 'Already applied');
                }

                

                return Application::create($fields);
            });
        }

    public function store_mysql(StoreApplicationReq $request){

        $fields = $request->validated();

            try {
                // We use DB::statement to run the CALL command
                DB::statement("CALL sp_store_application(?, ?, ?, ?)", [
                    $fields['user_id'],
                    $fields['internship_id'],
                    $fields['group_id'],
                    $fields['motivation_letter']
                ]);

                return response()->json(['message' => 'Application submitted!'], 201);

            } catch (\Illuminate\Database\QueryException $e) {
                // This catches the 'SIGNAL SQLSTATE' messages from the procedure
                return response()->json(['error' => $e->getMessage()], 400);
            }
            
        return Application::create($fields);
    }


}   
