<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Actor extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gender', 'birthday', 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Get the object record associated with the object. - Defining The Inverse Of The Relationship
     */
    public function translations() {
        return $this->hasMany('App\ActorTranslation');
    }

    public function translation($lang = null) {
        if ($lang == null) {
            $lang = App::getLocale();
        }

        return $this->hasMany('App\ActorTranslation', 'actor_id', 'id')->where('lang', '=', $lang)->first();
    }

    public function age() {
        if ($this->attributes['birthday'] > 0) {
            return floor((time() - strtotime($this->attributes['birthday'])) / 31556926); //31556926 is the number of seconds in a year.
        } else {
            return false;
        }
    }

    public function thumb() {
        
    }

    public function thumbs() {
        
    }

    public function videos() {
        
    }

    public function views($periond = null) {
        
    }

    public function comments() {
        
    }

    public function votes() {
        
    }

}
