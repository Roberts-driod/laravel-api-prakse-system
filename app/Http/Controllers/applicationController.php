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

            return Application::storeWithValidation($fields);
            
        }

    public function store_mysql(StoreApplicationReq $request){

        $fields = $request->validated();

            try {
                // We use DB::statement to run the CALL command
                Application::storeViaProcedure($fields);

                return response()->json(['message' => 'Application submitted!'], 201);

            } catch (\Illuminate\Database\QueryException $e) {
                // This catches the 'SIGNAL SQLSTATE' messages from the procedure
                return response()->json(['error' => $e->getMessage()], 400);
            }
            
        return Application::create($fields);
    }

    public function store_comment(Request $request){


            $validated = $request->validate([
                'comment' => 'required|string|max:1000',
            ]);

            try {

                    $application = Application::where('user_id', 1)
                        ->where('internship_id', 1)
                        ->where('group_id', 1)
                        ->firstOrFail();

                    $comment = $application->comments()->create([
                        'body' => $validated['comment']
                    ]);

                    return response()->json([
                        'message' => 'Comment created successfully',
                        'data' => $comment
                    ], 201);

                } catch (\Exception $e) {
                    return response()->json(['error' => 'Resource not found'], 404);
                }

    }


}   
