<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class comments extends Model
{
     protected  $fillable = ["body"];

     public function commentable()
        {
            return $this->morphTo();
        }


}
