<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Term extends Authenticatable {

    use Notifiable;
    //protected $table = 'terms';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'term_type_id', 'slug', 'parent_id', 'sort_i',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function termType() {
        return $this->belogsTo('App\TermType', 'term_type_id');
    }

}
