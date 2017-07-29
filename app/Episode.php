<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Episode extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'show_id', 'thetvdb_id', 'imdb_id', 'first_aired', 'season_number', 'episode_number', 'rating', 'rating_count', 'last_updated', 'active'
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
        return $this->hasMany('App\EpisodeTranslation');
    }

    public function getSeasonNumberAttribute() {
        return str_pad($this->attributes['season_number'], 2, "0", STR_PAD_LEFT);
    }

    public function getEpisodeNumberAttribute() {
        return str_pad($this->attributes['episode_number'], 2, "0", STR_PAD_LEFT);
    }

    public function translation($lang = null) {
        $lang = isset($lang) ? $lang : DEF_TRANSLATION;
        $translation = $this->hasMany('App\EpisodeTranslation', 'episode_id', 'id')->where('lang', '=', $lang)->first();
        if (!$translation->title) {
            $translations = $this->hasMany('App\EpisodeTranslation', 'episode_id', 'id')->get();
            foreach ($translations as $tr) {
                if (!$tr->title) {
                    continue;
                } else {
                    return $tr;
                }
            }
            $translation->title = 'TBA';
        }
        return $translation;
    }

    public function thumb() {//TODO::MORPH BY
        return $this->hasOne('App\File', 'model_id', 'id')->where('model_type', '=', 'App\Episode')->first();
    }

    public function views($periond = null) {
        
    }

    public function comments() {
        
    }

    public function votes() {
        
    }

}
