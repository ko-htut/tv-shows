<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Show;

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
        return str_pad($this->attributes['season_number'], 2, '0', STR_PAD_LEFT);
    }

    public function getEpisodeNumberAttribute() {
        return str_pad($this->attributes['episode_number'], 2, '0', STR_PAD_LEFT);
    }

    public function translation($lang = null) {
        /*
          $lang = isset($lang) ? $lang : DEF_TRANSLATION;
          $translation = $this->hasMany('App\EpisodeTranslation', 'episode_id', 'id')->where('lang', '=', $lang)->first();
          if (!isset($translation) || !$translation->title) {
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
         */
        $lang = isset($lang) ? $lang : DEF_TRANSLATION;
        $translation = $this->hasMany('App\EpisodeTranslation', 'episode_id', 'id')->where('lang', $lang)->first();

        $temp = [];

        if (isset($translation)) {

            if ($translation->title && $translation->content) {
                return $translation;
            }

            if (!empty($translation->title)) {
                $temp['title'] = $translation->title;
            }

            if (!empty($translation->content)) {
                $temp['content'] = $translation->content;
            }
        }


        $translations = $this->hasMany('App\EpisodeTranslation', 'episode_id', 'id')->get();
        foreach ($translations as $tr) {

            if (!$tr->title && !$tr->content) {
                continue;
            }

            if (!isset($temp['title']) && !empty($tr->title)) {
                $temp['title'] = $tr->title;
            }

            if (!isset($temp['content']) && !empty($tr->content)) {
                $temp['content'] = $tr->content;
            }

            if (isset($temp['title']) && isset($temp['content'])) {
                $tr->title = $temp['title'];
                $tr->content = $temp['content'];
                return $tr;
            }
        }

        if (isset($translation)) {
            $translation->title = isset($temp['title']) ? $temp['title'] : 'TBA';
            $translation->content = isset($temp['content']) ? $temp['content'] : '...';
        } else {
            $translation = new EpisodeTranslation();
            $translation->title = isset($temp['title']) ? $temp['title'] : 'TBA';
            $translation->content = isset($temp['content']) ? $temp['content'] : '...';
        }
        return $translation;
    }

    public function thumb() {
        $thumb = $this->hasMany('App\File', 'model_id', 'id')->where('model_type', '=', 'App\Episode')->where('type', '=', 'thumb')->first();
        return $thumb;
    }

    public function url($lang = null) {
        $show = Show::find($this->show_id);
        $lang = isset($lang) ? $lang : DEF_LANG;
        $prefix = $show->url($lang) . '/';
        $slug = 's' . str_pad($this->season_number, 2, '0', STR_PAD_LEFT) . 'e' . str_pad($this->episode_number, 2, '0', STR_PAD_LEFT);
        return $prefix . $slug;
    }

    public function views($periond = null) {
        
    }

    public function comments() {
        return $this->morphMany('App\Comment', 'model');
    }

    public function getTypeAttribute() {
        return get_class($this);
    }

    public function votes() {
        
    }

    public function next() {
        $next = Episode::where('show_id', $this->show_id)
                ->where('season_number', $this->season_number)
                ->where('season_number', '>', 0)
                ->where('episode_number', $this->episode_number + 1)
                ->first();
        if (!$next) {
            $next = Episode::where('show_id', $this->show_id)
                    ->where('season_number', $this->season_number + 1)
                    ->where('season_number', '>', 0)
                    ->where('episode_number', 1)
                    ->first();
        }
        return $next;
    }

    public function prev() {
        $prev = Episode::where('show_id', $this->show_id)
                ->where('season_number', $this->season_number)
                ->where('season_number', '>', 0)
                ->where('episode_number', $this->episode_number - 1)
                ->first();
        if (!$prev) {
            $prev = Episode::where('show_id', $this->show_id)
                    ->where('season_number', $this->season_number - 1)
                    ->where('episode_number', \DB::table('episodes')
                            ->where('show_id', $this->show_id)
                            ->where('season_number', $this->season_number - 1)
                            ->where('season_number', '>', 0)
                            ->max('episode_number'))
                    ->first();
        }
        return $prev;
    }

}
