<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['title', 'type'];

    protected static function boot()
    {
        static::creating(function (Question $question) {
            return $question->checkTypeValidity();
        });

        static::saving(function (Question $question) {
            return $question->checkTypeValidity();
        });
    }

    public function checkTypeValidity()
    {
        if (!in_array($this->type, AnswerTrait::$allowedTypes)) {
            abort(400, sprintf(
                    'Seuls les types de réponses "%s" sont autorisés.',
                    implode(', ', AnswerTrait::$allowedTypes)
                )
            );
        }

        return true;
    }

    public function answers()
    {
        return $this->hasMany($this->type);
    }
}
