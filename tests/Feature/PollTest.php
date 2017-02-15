<?php

namespace Tests\Feature;

use App\AnswerCheckbox;
use App\Poll;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\HelperTrait;
use Tests\TestCase;

class PollTest extends TestCase
{
    use DatabaseMigrations;
    use HelperTrait;


    public function testPollsCreationWithUsers()
    {
        // Création des utilisateurs
        $user_john = factory(User::class)->create(['first_name' => 'John']);
        $user_nick = factory(User::class)->create(['first_name' => 'Nick']);

        // On vérifie s'il n'y a pas de sondage
        $this->assertEquals($user_john->polls()->count(), 0);
        $this->assertEquals($user_nick->polls()->count(), 0);
        $this->assertEquals(Poll::count(), 0);

        // John crée son premier sondage
        $poll = $user_john->polls()->create([
            'title' => 'Le sondage de John',
            'description' => 'Ma description'
        ]);

        // On a donc un nouveau sondage en bdd, et c'est celui de John
        $this->assertEquals(Poll::count(), 1);
        $this->assertEquals($user_john->polls()->count(), 1);
        $this->assertEquals($poll->user->id, $user_john->id);
        $this->assertEquals($user_nick->polls()->count(), 0);
        $this->assertNotEquals($poll->user->id, $user_nick->id);
        $this->assertArraySubset([
            'title' => 'Le sondage de John'
        ], $user_john->polls()->first()->toArray());

        // Maintenant c'est Nick qui crée son sondage
        $poll = $user_nick->polls()->create([
            'title' => 'Le sondage de Nick',
            'description' => 'Ma description'
        ]);

        // Il y a maintenant 2 sondages, et le dernier c'est celui de Nick
        $this->assertEquals(Poll::count(), 2);
        $this->assertEquals($poll->user->id, $user_nick->id);
        $this->assertEquals($user_john->polls()->count(), 1);
        $this->assertNotEquals($poll->user->id, $user_john->id);
        $this->assertEquals($user_nick->polls()->count(), 1);
        $this->assertArraySubset([
            'title' => 'Le sondage de Nick'
        ], $user_nick->polls()->first()->toArray());
    }

    public function testPollToArray()
    {
        $poll = $this->bootstrapPoll($now);

        $this->assertArraySubset([
            'title' => 'My title',
            'description' => 'My description',
            'user_id' => 1,
            'updated_at' => (string)$now,
            'created_at' => (string)$now,
            'id' => 1,
        ], $poll->toArray());
    }

    public function testPollWithUserToArray()
    {
        $poll = $this->bootstrapPoll($now);

        $this->assertArraySubset([
            'title' => 'My title',
            'description' => 'My description',
            'user_id' => 1,
            'updated_at' => (string)$now,
            'created_at' => (string)$now,
            'id' => 1,
            'user' => [
                'first_name' => 'John'
            ],
        ], $poll->with('user')->first()->toArray());

    }

    public function testPollWithUserAndPagesToArray()
    {
        $poll = $this->bootstrapPoll($now);
        $this->attachPages($poll);

        $this->assertArraySubset([
            'title' => 'My title',
            'description' => 'My description',
            'user_id' => 1,
            'updated_at' => (string)$now,
            'created_at' => (string)$now,
            'id' => 1,
            'user' => [
                'first_name' => 'John'
            ],
            'pages' => [
                [
                    'title' => 'My 1st page'
                ],
                [
                    'title' => 'My 2nd page'
                ]
            ]
        ], $poll->with('user', 'pages')->first()->toArray());
    }

    public function testPollWithUserAndPagesAndQuestionsToArray()
    {
        $poll = $this->bootstrapPoll($now);
        $this->attachPages($poll);
        $this->attachQuestions($poll);

        $this->assertArraySubset([
            'title' => 'My title',
            'description' => 'My description',
            'user_id' => 1,
            'updated_at' => (string)$now,
            'created_at' => (string)$now,
            'id' => 1,
            'user' => [
                'first_name' => 'John'
            ],
            'pages' => [
                [
                    'title' => 'My 1st page',
                    'questions' => [
                        [
                            'title' => 'Question 1.1',
                            'type' => AnswerCheckbox::class,
                        ]
                    ]
                ]
            ]
        ], $poll->with('user', 'pages', 'pages.questions')->first()->toArray());
    }
}
