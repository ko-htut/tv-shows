<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Actor;
use DB;

class Show extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'thetvdb_id', 'imdb_id', 'first_aired', 'last_aired', 'ended', 'air_day', 'air_time', 'rating', 'rating_count', 'runtime', 'last_updated', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /* Files */

    public function files() {
        return $this->morphMany('App\File', 'model');
    }

    public function fanart() {
        return $this->files()->where('type', 'fanart')->orderBy('sort', 'desc')->first();
    }

    public function fanarts() {
        return $this->files()->where('type', 'fanart')->orderBy('sort', 'desc')->get();
    }

    public function poster() {
        return $this->files()->where('type', 'poster')->orderBy('sort', 'desc')->first();
    }

    public function posters() {
        return $this->files()->where('type', 'poster')->orderBy('sort', 'desc')->get();
    }

    /* Comments */

    public function comments() {
        return $this->morphMany('App\Comment', 'model');
    }

    /* Actors */

    public function actors() {
        return $this->morphToMany('App\Actor', 'model', 'actors_to_models');
    }

    public function actorsLimit($limit = 6) {
        return $this->actors()->groupBy('slug')->limit($limit)->orderBy('sort', 'asc')->get();
    }

    /* Terms */

    public function genres() {
        return $this->morphToMany('App\Term', 'model', 'terms_to_models');
    }

    /* Episodes */

    public function episodes() {
        return $this->hasMany('App\Episode');
    }

    public function seasonEpisodes($season = null) {
        return $this->episodes()->where('season_number', $season)->groupBy(DB::raw('episode_number'))->orderBy('first_aired')->get();
    }

    public function seasonEpisodesCount($season = null) {
        return count(DB::table('episodes')->selectRaw('count(*)')->where('show_id', $this->id)->where('season_number', $season)->groupBy('episode_number')->get());
    }

    public function lastSeason() {
        return $this->hasMany('App\Episode')->max('season_number');
    }

    public function firstSeason() {
        return $this->hasMany('App\Episode')->where('season_number', '>', 0)->min('season_number');
    }

    /**
     * Get the object record associated with the object. - Defining The Inverse Of The Relationship
     */
    public function translations() {
        return $this->hasMany('App\ShowTranslation');
    }

    public function translation($lang = null) {

        $lang = isset($lang) ? $lang : DEF_TRANSLATION;
        $translation = $this->hasMany('App\ShowTranslation', 'show_id', 'id')->where('lang', '=', $lang)->first();

        if ((isset($translation) && !$translation->title) || $translation == null) {
            $translations = $this->hasMany('App\ShowTranslation', 'show_id', 'id')->get();
            foreach ($translations as $tr) {
                if (!$tr->title) {
                    continue;
                } else {
                    return $tr;
                }
            }
        }
        return $translation;
    }
    

    public function url($lang = null) {
        $lang = isset($lang) ? $lang : DEF_LANG;
        $prefix = ($lang == DEF_LANG) ? '/shows/' : '/' . $lang . '/shows/';
        $slug = null;
        if ($this->translation($lang) !== null) {
            $slug = $this->translation($lang)->slug;
        }
        if (!empty($slug)) {
            return $prefix . $slug;
        } else {
            return $prefix . $this->id;
        }
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

    public function getLastAiredAttribute() {
        if ($this->attributes['ended'] == true) {
            $lastEpisodeAired = DB::table('episodes')->where('show_id', $this->id)->max('first_aired');
            return date('Y', strtotime($lastEpisodeAired));
        }
        return '';
    }

    public function views($periond = null) {
        
    }

    public function getTypeAttribute() {
        return get_class($this);
    }

    public function votes() {
        
    }

}
