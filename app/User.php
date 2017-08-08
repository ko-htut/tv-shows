<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_role_id', 'username', 'password', 'email', 'first_name', 'last_name', 'lang', 'birthday', 'about', 'facebook_id', 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function age() {
        if ($this->attributes['birthday'] > 0) {
            return floor((time() - strtotime($this->attributes['birthday'])) / 31556926); //31556926 is the number of seconds in a year.
        } else {
            return false;
        }
    }
    
     public function getTypeAttribute() {
        return get_class($this);
    }
    
    public function avatar(){
        return $this->morphOne('App\File', 'model');
    }
    
    public function avatarPlaceholder(){
        return '/storage/app/public/img/placeholders/user.png';
    }

}
