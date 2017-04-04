<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OptionTranslation extends Authenticatable {

    use Notifiable;

    protected $table = 'options_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    
    protected $fillable = [
        'option_id', 'lang', 'value', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function language() {
        return $this->belongsTo('App\Language');
    }

    public function option() {
        return $this->belogsTo('App\Option');
    }

}
