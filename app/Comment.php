<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Comment extends Authenticatable {

    use Notifiable;

    protected $fillable = [
        'user_id', 'parent_id', 'title', 'content', 'lang', 'model_id', 'model_type', 'active',
    ];
    
    public function model(){
        return $this->morphTo();
    }
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function replies(){
       //::TODO
    }

}
