<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ActorTranslation extends Authenticatable {

    use Notifiable;

    protected $table = 'actors_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'actor_id', 'lang', 'title', 'slug', 'content', 'meta_title', 'meta_description', 'active',
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

    public function actor() {
        return $this->belogsTo('App\Actor');
    }

}
