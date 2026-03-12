<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    protected $fillable = ['name', 'goals'];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_internship')
            ->withPivot('start_at', 'end_at');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}