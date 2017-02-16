<?php

namespace App;

trait AnswerTrait
{
    public static $allowedTypes = [
        AnswerCheckbox::class,
        AnswerRadio::class,
        AnswerLinearScale::class
    ];

    public function checkTypeValidity($type)
    {
        if (!in_array($type, self::$allowedTypes)) {
            abort(400, sprintf(
                    'Seuls les types de réponses "%s" sont autorisés.',
                    implode(', ', self::$allowedTypes)
                )
            );
        }

        return true;
    }
}
