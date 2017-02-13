<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['title'];

    public $timestamps = false;

    public function questions() {
        return $this->hasMany(Question::class);
    }
}
