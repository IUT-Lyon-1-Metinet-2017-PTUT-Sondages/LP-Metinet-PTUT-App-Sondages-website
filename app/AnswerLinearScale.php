<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerLinearScale extends Model
{
    use AnswerTrait;

    public $timestamps = false;

    protected $table = 'answers_linear_scale';

    protected $fillable = ['count', 'value', 'text'];
}
