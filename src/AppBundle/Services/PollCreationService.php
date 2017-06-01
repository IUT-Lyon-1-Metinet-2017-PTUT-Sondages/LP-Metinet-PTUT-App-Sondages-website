<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 07/04/17
 * Time: 12:01
 */

namespace AppBundle\Services;

use AppBundle\Entity\Page;
use AppBundle\Entity\Poll;
use AppBundle\Entity\Proposition;
use AppBundle\Entity\Question;
use AppBundle\Entity\User;

/**
 * @package AppBundle\Services
 */
class PollCreationService
{
    public $variantRepositoryService;
    public $pollRepositoryService;
    public $pageRepositoryService;
    public $questionRepositoryService;
    public $propositionRepositoryService;

    /**
     * PollCreationService constructor.
     * @param PollRepositoryService        $pollRepositoryService
     * @param PageRepositoryService        $pageRepositoryService
     * @param QuestionRepositoryService    $questionRepositoryService
     * @param PropositionRepositoryService $propositionRepositoryService
     */
    public function __construct(
        PollRepositoryService $pollRepositoryService,
        PageRepositoryService $pageRepositoryService,
        QuestionRepositoryService $questionRepositoryService,
        PropositionRepositoryService $propositionRepositoryService
    ) {
        $this->pollRepositoryService = $pollRepositoryService;
        $this->pageRepositoryService = $pageRepositoryService;
        $this->questionRepositoryService = $questionRepositoryService;
        $this->propositionRepositoryService = $propositionRepositoryService;
    }

    /**
     * @param Poll        $poll
     * @param Page        $page
     * @param Question    $question
     * @param Proposition $proposition
     * @param User        $user
     */
    public function createPoll($poll, $page, $question, $proposition, $user)
    {
        $this->pollRepositoryService->createPoll($poll, $user);
        $this->pageRepositoryService->createPage($page);
        $this->questionRepositoryService->createQuestion($question);
        $this->propositionRepositoryService->createProposition($proposition);
    }
}
