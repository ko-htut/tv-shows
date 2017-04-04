<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Page extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'active',
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
        return $this->hasMany('App\PageTranslation');
    }

    public function translation($lang = null) {
        if ($lang == null) {
            $lang = App::getLocale();
        }

        return $this->hasMany('App\PageTranslation', 'page_id', 'id')->where('lang', '=', $lang)->first();
    }

}
