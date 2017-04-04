<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Select extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'type', 'parent_id', 'active',
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
    public function options() {//'select', 'multiselect', 'range', 'radio'//::TODO RADIO
        switch ($this->attributes['type']) {
            case in_array($this->attributes['type'], ['multiselect']);
                return $this->hasMany('App\Options');
            case in_array($this->attributes['type'], ['select', 'radio']);
                return $this->hasOne('App\Options');
            default:
                return $this->hasMany('App\Options');
        }
    }
    //::TODO
    public function optionsTranslations($lang = null) {
        if ($lang == null) {
            $lang = App::getLocale();
        }
        return $this->hasMany('App\ChoiceTranslation', 'choice_id', 'id')->where('lang', '=', $lang)->first();
    }

}
