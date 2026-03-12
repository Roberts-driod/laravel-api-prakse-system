<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function internships()
    {
        return $this->belongsToMany(Internship::class, 'group_internship')
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