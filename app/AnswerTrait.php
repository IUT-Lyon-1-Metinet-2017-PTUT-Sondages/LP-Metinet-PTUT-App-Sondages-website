<?php

namespace App;

trait AnswerTrait
{
    public static $allowedTypes = [AnswerCheckbox::class, AnswerRadio::class, AnswerLinearScale::class];
}
