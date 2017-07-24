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
        'thetvdb_id', 'name', 'role', 'sort', 'active',
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
        $thumb = $this->hasOne('App\File', 'model_id', 'id')->where('model_type', '=', 'App\Actor')->where('type', '=', 'thumb')->first();


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
