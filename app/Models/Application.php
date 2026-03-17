<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Internship;

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

        public static function storeViaProcedure($fields) {
        return DB::statement("CALL sp_store_application(?, ?, ?, ?)", [
            $fields['user_id'], $fields['internship_id'], $fields['group_id'], $fields['motivation_letter']
            ]);
        }
}