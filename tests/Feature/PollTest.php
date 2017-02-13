<?php

namespace Tests\Feature;

use App\AnswerCheckbox;
use App\Poll;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PollTest extends TestCase
{
    use DatabaseMigrations;

    private function createUser($firstname)
    {
        return User::create([
            'first_name' => $firstname,
            'last_name' => 'Smith',
            'email' => strtolower($firstname) . '@smi.th',
            'password' => Hash::make('foo'),
            'is_active' => false,
            'is_admin' => true,
        ]);
    }

    public function testPollsCreationWithUsers()
    {
        // Création des utilisateurs
        $user_john = $this->createUser('John');
        $user_nick = $this->createUser('Nick');

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
}
