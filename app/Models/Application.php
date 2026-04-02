<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Internship;
use App\Models\User;
use App\Models\Group;

class Application extends Model
{
    public $incrementing = false;
    protected $primaryKey = ['user_id', 'internship_id', 'group_id'];
    protected $keyType = 'array';

    protected $fillable = ['user_id', 'internship_id', 'group_id', 'approved_at', 'motivation_letter'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function internship()
    {
        return $this->belongsTo(Internship::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

        public static function storeWithValidation(array $data)
    {

    try {
        return DB::transaction(function () use ($data) {
            $user = User::find($data['user_id']);
            if (!$user) abort(404, 'User not found');

            $internship = Internship::find($data['internship_id']);
            if (!$internship) abort(404, 'Internship not found');

            if ($user->role->role !== "student") {
                abort(403, 'No permission');
            }

            $exists = self::where('user_id', $data['user_id'])
                ->where('internship_id', $data['internship_id'])
                ->exists();

            if ($exists) {
                abort(409, 'Already applied');
            }

            return self::create($data);
        });
    }
            catch (\Throwable $e) {

            $userExists = User::where('id', $data['user_id'])->exists();
            $groupExists = Group::where('id', $data['group_id'])->exists();

            if (!$userExists) {
                self::logFailure(null, 'User not found'); 
                abort(404, 'User not found');
            }

            if (!$groupExists) {
                self::logFailure(null, 'Group not found'); 
                abort(404, 'Group not found');
            }            

            self::logFailure($data['user_id'] ?? null, $e->getMessage());
            throw $e;
        }

    }

        public static function storeViaProcedure($fields) {
        return DB::statement("CALL sp_store_application(?, ?, ?, ?)", [
            $fields['user_id'], $fields['internship_id'], $fields['group_id'], $fields['motivation_letter']
            ]);
        }

        public static function logFailure($userId , $reason) {

            DB::table('user_logs')->insert([
                'user_id' => $userId,
                'action' => 'FAILED_APPLICATION',
                'table_name' => 'applications',
                'record_id' => $userId,
                'details' => $reason,
                'created_at' => now()
            ]);

        }
}