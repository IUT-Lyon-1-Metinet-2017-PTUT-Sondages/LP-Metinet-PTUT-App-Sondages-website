<?php
/**
 * Created by PhpStorm.
 * User: kocal
 * Date: 15/02/17
 * Time: 19:37
 */

namespace Tests;


use App\AnswerCheckbox;
use App\AnswerRadio;
use App\Poll;
use App\User;
use Carbon\Carbon;

trait HelperTrait
{
    public function bootstrapPoll(&$now)
    {
        $now = Carbon::now();
        $user = factory(User::class)->create(['first_name' => 'John']);

        return $user->polls()->create([
            'title' => 'My title',
            'description' => 'My description',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }

    public function attachPages(Poll $poll)
    {
        $poll->pages()->create([
            'title' => 'My 1st page'
        ]);

        $poll->pages()->create([
            'title' => 'My 2nd page'
        ]);
    }

    public function attachQuestions(Poll $poll)
    {
        $poll->pages()->first()->questions()->create([
            'title' => 'Question 1.1',
            'type' => AnswerCheckbox::class
        ]);

        $poll->pages()->first()->questions()->create([
            'title' => 'Question 2.1',
            'type' => AnswerRadio::class
        ]);
    }

    public function attachAnswers(Poll $poll)
    {
        $poll->pages()->first()->questions()->first()->answers()->create([
            'value' => '1st answer',
        ]);

        $poll->pages()->first()->questions()->first()->answers()->create([
            'value' => '2nd answer',
        ]);
    }

}