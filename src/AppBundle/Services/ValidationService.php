<?php

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


use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

/**
 * Class ValidationService
 * @package AppBundle\Services
 */
class ValidationService
{
    /**
     * @var Validator
     */
    protected $validator;
    public $variantRepositoryService;


    public function __construct(
        Validator $validator,
        VariantRepositoryService $variantRepositoryService,
        PollRepositoryService $pollRepositoryService,
        PageRepositoryService $pageRepositoryService,
        QuestionRepositoryService $questionRepositoryService,
        PropositionRepositoryService $propositionRepositoryService
    ) {
        $this->validator = $validator;
        $this->variantRepositoryService = $variantRepositoryService;
        $this->pollRepositoryService = $pollRepositoryService;
        $this->pageRepositoryService = $pageRepositoryService;
        $this->questionRepositoryService = $questionRepositoryService;
        $this->propositionRepositoryService = $propositionRepositoryService;
    }

    public function validatePollRequest($request, $user)
    {
        if (null !== $request->get('poll')) {
            $poll = new Poll;
            $poll->setTitle($request->get('poll')['title']);
            $poll->setDescription($request->get('poll')['description']);

            $pollErrors = $this->validatePoll($poll);

            if (count($pollErrors) > 0) {
                return $pollErrors;
            }
            if (null !== $request->get('poll')['pages'] && isset($request->get('poll')['pages'])) {
                foreach ($request->get('poll')['pages'] as $key => $page) {
                    $thisPage = new Page;
                    $thisPage->setTitle($page['title']);
                    $thisPage->setDescription($page['description']);
                    $pageErrors = $this->validatePage($thisPage);

                    if (count($pageErrors) > 0) {
                        return $pageErrors;
                    }
                    $thisPage->setPoll($poll);
                    $poll->addPage($thisPage);
                    if (null !== $page['questions'] && isset($page['questions'])) {
                        foreach ($page['questions'] as $question) {
                            $thisQuestion = new Question;
                            $thisQuestion->setTitle($question['title']);


                            $questionErrors = $this->validateQuestion($question);

                            if (count($questionErrors) > 0) {
                                return $questionErrors;
                            }
                            $thisQuestion->setPage($thisPage);
                            $thisPage->addQuestion($thisQuestion);
                            $thisQuestion->setPoll($poll);
                            $poll->addQuestion($thisQuestion);


                            $variant = $this->variantRepositoryService->getVariant(['title' => $question['variant']]);
                            if (null !== $variant) {
                                $variantErrors = $this->validateVariant($variant);
                            } else {
                                return ['Variant does not exist ' . implode(",", $question['variant']) . '.. '];
                            }
                            if (count($variantErrors) > 0) {
                                return $variantErrors;
                            }

                            if (null !== $question['propositions'] && isset($question['propositions'])) {
                                foreach ($question['propositions'] as $proposition) {
                                    $thisProposition = new Proposition();
                                    $thisProposition->setTitle($proposition['title']);
                                    $thisProposition->setVariant($variant);
                                    $thisProposition->setQuestion($thisQuestion);
                                    $thisQuestion->addProposition($thisProposition);

                                    $propositionErrors = $this->validateProposition($proposition);

                                    if (count($propositionErrors) > 0) {
                                        return $propositionErrors;
                                    }
                                    $this->pollRepositoryService->createPoll($poll, $user);
                                    $this->pageRepositoryService->createPage($thisPage);
                                    $this->questionRepositoryService->createQuestion($thisQuestion);
                                    $this->propositionRepositoryService->createProposition($thisProposition);


                                }
                            }
                        }
                    }
                }
            }
        } else {
            throw $this->createException('No Poll sent');
        }

    }

    public function validatePoll($poll)
    {
        $errors = $this->validator->validate($poll);
        return $errors;
    }

    public function validatePage($page)
    {
        $errors = $this->validator->validate($page);

        return $errors;
    }

    public function validateVariant($variant)
    {
        $errors = $this->validator->validate($variant);
        return $errors;
    }

    public function validateQuestion($question)
    {
        $errors = $this->validator->validate($question);

        return $errors;
    }

    public function validateProposition($proposition)
    {
        $errors = $this->validator->validate($proposition);

        return $errors;
    }

    public function validateAnswer($answer)
    {
        $errors = $this->validator->validate($answer);

        return $errors;
    }

}
