<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Cocur\Slugify\Slugify;
use DB;

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

    public function age() {
        if ($this->attributes['birthday'] > 0) {
            return floor((time() - strtotime($this->attributes['birthday'])) / 31556926); //31556926 is the number of seconds in a year.
        } else {
            return false;
        }
    }

    public function thumb() {
        $thumb = $this->hasOne('App\File', 'model_id', 'id')->where('model_type', '=', 'App\Actor')->where('type', '=', 'thumb')->first();
        if ($thumb) {
            $ch = curl_init($thumb->external_patch);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($retcode != 200) {
                $cachedUrl = 'http://thetvdb.com/' . '/banners/_cache/actors/' . $this->thetvdb_id . '.jpg';
                $ch = curl_init($cachedUrl);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_exec($ch);
                $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($retcode == 200) {
                    $thumb->external_patch = $cachedUrl;
                    return $thumb;
                } else {
                    $thumb->external_patch = 'http://thetvdb.com/' . '/banners/actors/0.jpg';
                    return $thumb;
                }
            } else {
                return $thumb;
            }
        }
        return null;
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

    public function url($lang = null) {
        $lang = isset($lang) ? $lang : DEF_LANG;
        $slug = $this->slug;
        $prefix = ($lang == DEF_LANG) ? '/actors/' : '/' .$lang . '/actors/';
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

}
