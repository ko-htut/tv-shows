<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Lang;
use DB;

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
        return $this->hasOne('App\File', 'model_id', 'id')->where('model_type', '=', 'App\Show')->first(); //
    }

    public function allEpisodes() {
        return $this->hasMany('App\Episode')->where('season_number', '>', 0);
    }

    public function seasonEpisodes($season = null) {
        return $this->hasMany('App\Episode')->where('season_number', '=', $season)->get();
    }

    public function seasonEpisodesCount($season = null) {
        return $this->hasMany('App\Episode')->where('season_number', '=', $season)->count();
    }

    public function lastSeason() {
        return $this->hasMany('App\Episode')->max('season_number');
    }

    public function genres() {
        //$term_type_id = TermType::where('name', '=', 'genre')->first()->id;
        return $this->morphToMany('App\Term', 'model', 'terms_to_models', null, null);
    }

    public function status() {
        $select_id = Select::where('title', '=', __FUNCTION__)->first()->id;
        return $this->morphToMany('App\Option', 'model', 'options_to_models', null, null)->where('select_id', '=', $select_id)->first();
    }

    public function network() {
        $select_id = Select::where('title', '=', __FUNCTION__)->first()->id;
        return $this->morphToMany('App\Option', 'model', 'options_to_models', null, null)->where('select_id', '=', $select_id)->first();
    }

    public function content_rating() {
        $select_id = Select::where('title', '=', __FUNCTION__)->first()->id;
        return $this->morphToMany('App\Option', 'model', 'options_to_models', null, null)->where('select_id', '=', $select_id)->first();
    }

    public function getFirstAiredAttribute() {
       return date('Y', strtotime($this->attributes['first_aired']));
    }

    public function views($periond = null) {
        
    }

    public function comments() {
        
    }

    public function votes() {
        
    }

}
