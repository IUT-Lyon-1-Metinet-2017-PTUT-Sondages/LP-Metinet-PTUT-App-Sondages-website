<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerCheckbox extends Model implements AnswerInterface
{
    use AnswerTrait;

    public $timestamps = false;

    protected $table = 'answers_checkbox';

    protected $fillable = ['count', 'value'];
}
