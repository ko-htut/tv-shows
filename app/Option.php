<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Option extends Authenticatable {

    use Notifiable;
    //protected $table = 'options';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'select_id', 'slug', 'parent_id', 'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function select() {
        return $this->belogsTo('App\Select');
    }
    
    public function translations() {
        return $this->hasMany('App\OptionTranslation');
    }

    public function translation($lang = null) {
        if ($lang == null) {
            $lang = 'en';
        }
        return $this->hasMany('App\OptionTranslation', 'option_id', 'id')->where('lang', '=', $lang)->first();
    }
    
    public function url($lang = null) {
        $lang = isset($lang) ? $lang : DEF_LANG;
        $slug = $this->translation($lang)->slug;
        $prefix = ($lang == DEF_LANG) ? '/networks/' : '/' .$lang . '/networks/';
        return $prefix . $slug;
    }

}
