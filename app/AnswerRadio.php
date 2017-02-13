<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerRadio extends Model
{
    use AnswerTrait;

    public $timestamps = false;

    protected $table = 'answers_radio';

    protected $fillable = ['count', 'value'];
}
