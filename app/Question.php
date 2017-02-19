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

    /**
     * @param string|null $type
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers($type = null)
    {
        if (!is_string($type)) {
            $type = $this->type;
        }

        if (!$type) {
            throw new \InvalidArgumentException(
                "Impossible de faire la relation avec l'une des tables de réponses,"
                . " car cette relation doit se faire  avec la valeur de `questions`.`type`,"
                . " et le model Question n'est pas instancié."
            );
        }

        return $this->hasMany($type);
    }
}
