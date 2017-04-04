<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Lang;
//use App\Config;
class Show extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'thetvdb_id', 'imdb_id', 'first_aired', 'finale_aired', 'air_day', 'air_time', 'rating', 'rating_count', 'runtime', 'last_updated', 'active'
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
        return $this->hasMany('App\ShowTranslation');
    }

    public function translation($lang = null) {
        return $this->hasMany('App\ShowTranslation', 'show_id', 'id')->where('lang', '=', $lang)->first();
    }


    public function banner() {//TODO::MORPH BY
       return $this->hasOne('App\File', 'model_id', 'id')->where('model_type', '=', 'Show')->first();
    }

    public function episodes() {
        return $this->hasMany('App\Episode', 'show_id', 'id')->where('season_number', '>', 0);
    }
    
    public function views($periond = null) {
        
    }

    public function comments() {
        
    }

    public function votes() {
        
    }

}
