<?php

namespace Tests\Feature;

use App\AnswerCheckbox;
use App\AnswerLinearScale;
use App\AnswerRadio;
use App\Page;
use App\Poll;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class QuestionTest extends TestCase
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

    public function testQuestionGoodAnswerType()
    {
        $page = $this->bootstrapQuestionsCreation();

        $page->questions()->create([
            'title' => 'Titre',
            'type' => AnswerCheckbox::class,
        ]);

        $page->questions()->create([
            'title' => 'Titre',
            'type' => AnswerRadio::class,
        ]);

        $page->questions()->create([
            'title' => 'Titre',
            'type' => AnswerLinearScale::class,
        ]);
    }

    public function testCreateQuestionWithBadAnswerType()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessageRegExp('/Seuls les types de réponses.*sont autorisés./');
        $page = $this->bootstrapQuestionsCreation();

        $page->questions()->create([
            'title' => 'Titre',
            'type' => 'foo',
        ]);
    }

    public function testSaveQuestionWithBadAnswerType()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessageRegExp('/Seuls les types de réponses.*sont autorisés./');

        $page = $this->bootstrapQuestionsCreation();

        $question = $page->questions()->create([
            'title' => 'Titre',
            'type' => AnswerCheckbox::class,
        ]);

        $question->type = 'foo';
        $question->save();
    }
}
