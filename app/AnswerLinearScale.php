<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerLinearScale extends Model implements AnswerInterface
{
    use AnswerTrait;

    public $timestamps = false;

    protected $table = 'answers_linear_scale';

    protected $fillable = ['count', 'value', 'text'];
}
