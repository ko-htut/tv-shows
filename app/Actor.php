<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Cocur\Slugify\Slugify;
use DB;
use App\File;

class Actor extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'thetvdb_id', 'name', 'slug', 'role', 'sort', 'active',
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

    public function thumb() {
        return $this->files()->where('type', 'thumb')->orderBy('sort', 'desc')->first();
    }

    public function placeholder() {
        return '/storage/app/public/img/placeholders/actor.jpg';
    }

    /* Comments */

    public function comments() {
        return $this->morphMany('App\Comment', 'model');
    }

    /* Shows */
    public function shows() {
        return $this->morphedByMany('App\Show', 'model', 'actors_to_models');
    }

    /* Functions */

    /* Returns this class name App\Actor */

    public function getTypeAttribute() {
        return get_class($this);
    }

    public function url($lang = null) {
        $lang = isset($lang) ? $lang : DEF_LANG;
        $slug = $this->slug;
        $prefix = ($lang == DEF_LANG) ? '/actors/' : '/' . $lang . '/actors/';
        return $prefix . $slug;
    }

    public function getSlug() {
        $slugify = new Slugify();
        return $slugify->slugify($this->name);
    }

    public function setSlug() {
        $slugify = new Slugify();
        $slug = $slugify->slugify($this->name);
        if (strlen($slug) > 1) {
            DB::table('actors')
                    ->where('id', $this->id)
                    ->update(['slug' => $slug]);
        }
    }

    /* TODO */

    public function votes() {
        
    }

    public function views($periond = null) {
        
    }

}
