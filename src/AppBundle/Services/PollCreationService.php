<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 07/04/17
 * Time: 12:01
 */

namespace AppBundle\Services;

use AppBundle\Entity\Poll;
use AppBundle\Entity\Question;
use AppBundle\Entity\Variant;
use AppBundle\Entity\Page;
use AppBundle\Entity\Proposition;


use AppBundle\Services\VariantRepositoryService;
use AppBundle\Services\PollRepositoryService;
use AppBundle\Services\QuestionRepositoryService;
use AppBundle\Services\PropositionRepositoryService;
use AppBundle\Services\PageRepositoryService;
use AppBundle\Services\ValidationService;

/**
 * Class ValidationService
 * @package AppBundle\Services
 */
class PollCreationService
{
    /**
     * @var Validator
     */
    protected $validationService;
    public $variantRepositoryService;
    public $pollRepositoryService;
    public $pageRepositoryService;
    public $questionRepositoryService;
    public $propositionRepositoryService;


    public function __construct(
        ValidationService $validationService,
        VariantRepositoryService $variantRepositoryService,
        PollRepositoryService $pollRepositoryService,
        PageRepositoryService $pageRepositoryService,
        QuestionRepositoryService $questionRepositoryService,
        PropositionRepositoryService $propositionRepositoryService
    ) {
        $this->validationService = $validationService;
        $this->variantRepositoryService = $variantRepositoryService;
        $this->pollRepositoryService = $pollRepositoryService;
        $this->pageRepositoryService = $pageRepositoryService;
        $this->questionRepositoryService = $questionRepositoryService;
        $this->propositionRepositoryService = $propositionRepositoryService;
    }

    public function createPoll($poll, $page, $question, $proposition, $user)
    {
        $this->pollRepositoryService->createPoll($poll, $user);
        $this->pageRepositoryService->createPage($page);
        $this->questionRepositoryService->createQuestion($question);
        $this->propositionRepositoryService->createProposition($proposition);
    }


}