<?php

namespace Tests\Feature;

use App\AnswerCheckbox;
use App\AnswerLinearScale;
use App\AnswerRadio;
use App\Page;
use App\Poll;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AnswerTest extends TestCase
{
    use DatabaseMigrations;

    private function bootstrapQuestionsCreation()
    {
        // Vive les factory, j'suis trop feignant..
        $user = factory(User::class)->create();
        $poll = $user->polls()->save(factory(Poll::class)->make());
        $page = $poll->pages()->save(factory(Page::class)->make());

        return $page;
    }

    public function testAnswerCheckboxCreation()
    {
        $page = $this->bootstrapQuestionsCreation();
        $question = $page->questions()->create([
            'title' => 'Titre',
            'type' => AnswerCheckbox::class,
        ]);

        $answer = $question->answers()->create(['value' => 'Foo']);
        $question->answers()->create(['value' => 'Bar']);
        $question->answers()->create(['value' => 'FooBar']);

        $this->assertInstanceOf(AnswerCheckbox::class, $answer);
        $this->assertNotInstanceOf(AnswerRadio::class, $answer);
        $this->assertNotInstanceOf(AnswerLinearScale::class, $answer);
    }

    public function testAnswerRadioCreation()
    {
        $page = $this->bootstrapQuestionsCreation();
        $question = $page->questions()->create([
            'title' => 'Titre',
            'type' => AnswerRadio::class,
        ]);

        $answer = $question->answers()->create(['value' => 'Foo']);
        $question->answers()->create(['value' => 'Bar']);
        $question->answers()->create(['value' => 'FooBar']);

        $this->assertNotInstanceOf(AnswerCheckbox::class, $answer);
        $this->assertInstanceOf(AnswerRadio::class, $answer);
        $this->assertNotInstanceOf(AnswerLinearScale::class, $answer);
    }

    public function testAnswerLinearScaleCreation()
    {
        $page = $this->bootstrapQuestionsCreation();
        $question = $page->questions()->create([
            'title' => 'Titre',
            'type' => AnswerLinearScale::class,
        ]);

        $answer = $question->answers()->create(['value' => 1, 'text' => 'foo']);
        $question->answers()->create(['value' => 2]);
        $question->answers()->create(['value' => 3, 'text' => 'bar']);

        $this->assertNotInstanceOf(AnswerCheckbox::class, $answer);
        $this->assertNotInstanceOf(AnswerRadio::class, $answer);
        $this->assertInstanceOf(AnswerLinearScale::class, $answer);
    }
}
