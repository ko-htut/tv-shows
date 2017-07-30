<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Actor;

class Show extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'thetvdb_id', 'imdb_id', 'first_aired', 'ended', 'air_day', 'air_time', 'rating', 'rating_count', 'runtime', 'last_updated', 'active'
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
        $lang = isset($lang) ? $lang : DEF_TRANSLATION;
        $translation = $this->hasMany('App\ShowTranslation', 'show_id', 'id')->where('lang', '=', $lang)->first();
        if (isset($translation) && !$translation->title) {
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

    public function fanart() {
        $fanarts = $this->hasMany('App\File', 'model_id', 'id')->where('model_type', '=', 'App\Show')->where('type', '=', 'fanart')->orderBy('sort', 'desc')->get(); //
        //return $fanarts[0];
        //dd($fanarts);
        foreach ($fanarts as $fanart) {

            $ch = curl_init($fanart->external_patch);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            //$retcode >= 400 -> not found, $retcode = 200, found.
            curl_close($ch);
            if ($retcode == 200) {
                return $fanart;
            } else {
                \App\File::destroy($fanart->id);
            }
        }
        return null;
    }

    public function fanarts() {
        $fanarts = $this->hasMany('App\File', 'model_id', 'id')->where('model_type', '=', 'App\Show')->where('type', '=', 'fanart')->orderBy('sort', 'desc')->limit(10)->get(); //
        foreach ($fanarts as &$fanart) {
            /*
              $ch = curl_init($fanart->external_patch);
              curl_setopt($ch, CURLOPT_NOBODY, true);
              curl_exec($ch);
              $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
              //$retcode >= 400 -> not found, $retcode = 200, found.
              curl_close($ch);
              if ($retcode != 200) {
              \App\File::destroy($fanart->id);
              unset($fanart);
              }
             */
        }
        return $fanarts;
        //dd($fanarts);
    }

    public function poster() {
        $posters = $this->hasMany('App\File', 'model_id', 'id')->where('model_type', '=', 'App\Show')->where('type', '=', 'poster')->orderBy('sort', 'desc')->get(); //
        //return $fanarts[0];
        //dd($fanarts);
        foreach ($posters as $poster) {

            $ch = curl_init($poster->external_patch);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            //$retcode >= 400 -> not found, $retcode = 200, found.
            curl_close($ch);
            if ($retcode == 200) {
                return $poster;
            } else {
                \App\File::destroy($poster->id);
            }
        }
        return null;
    }

    public function posters() {
        $posters = $this->hasMany('App\File', 'model_id', 'id')->where('model_type', '=', 'App\Show')->where('type', '=', 'poster')->orderBy('sort', 'desc')->get(); //
        foreach ($posters as &$file) {
            $ch = curl_init($file->external_patch);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            //$retcode >= 400 -> not found, $retcode = 200, found.
            curl_close($ch);
            if ($retcode != 200) {
                \App\File::destroy($file->id);
                unset($file);
            }
        }
        return $posters;
    }

    public function actors($limit = 10) {
        //return $this->morphToMany('App\Actor', 'model', 'actors_to_models');
        $actors = Actor::join('actors_to_models as pivot', 'pivot.actor_id', '=', 'actors.id')
                ->where('model_id', '=', $this->id)
                ->where('model_type', '=', 'App\Show')
                ->select('actors.*')// just to avoid fetching anything from joined table
                ->orderBy('sort', 'asc')
                ->limit($limit)
                ->distinct()
                ->get();
        return $actors;
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
