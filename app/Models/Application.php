<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}