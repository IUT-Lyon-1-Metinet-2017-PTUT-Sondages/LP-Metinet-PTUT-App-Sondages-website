<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use AnswerTrait;

    protected $fillable = ['title', 'type'];

    protected static function boot()
    {
        static::creating(function (Question $question) {
            return $question->checkTypeValidity($question->type);
        });

        static::saving(function (Question $question) {
            return $question->checkTypeValidity($question->type);
        });
    }

    public function answers()
    {
        return $this->hasMany($this->type);
    }
}
