<?php

namespace AppBundle\Services;

use AppBundle\Entity\Page;
use AppBundle\Entity\Poll;
use AppBundle\Entity\Proposition;
use AppBundle\Entity\Question;
use AppBundle\Entity\User;
use AppBundle\Entity\Variant;
use AppBundle\Exception\ValidationFailedException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface as Validator;

/**
 * Class ValidationService
 * @package AppBundle\Services
 */
class ValidationService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var ChartTypeRepositoryService
     */
    private $chartTypeRepositoryService;

    /**
     * @var VariantRepositoryService
     */
    private $variantRepositoryService;

    /**
     * @var PollCreationService
     */
    private $pollCreationService;

    /**
     * ValidationService constructor.
     *
     * @param EntityManager $entityManager
     * @param Validator $validator
     * @param ChartTypeRepositoryService $chartTypeRepositoryService
     * @param VariantRepositoryService $variantRepositoryService
     * @param PollCreationService $pollCreationService
     */
    public function __construct(
        EntityManager $entityManager,
        Validator $validator,
        ChartTypeRepositoryService $chartTypeRepositoryService,
        VariantRepositoryService $variantRepositoryService,
        PollCreationService $pollCreationService
    ) {
        $this->em = $entityManager;
        $this->validator = $validator;
        $this->chartTypeRepositoryService = $chartTypeRepositoryService;
        $this->variantRepositoryService = $variantRepositoryService;
        $this->pollCreationService = $pollCreationService;
    }

    /**
     * @param array  $array
     * @param string $entity
     * @return Poll|Page|Question|Proposition
     */
    public function findIfExistOrCreateNew(array $array, string $entity)
    {
        if (!array_key_exists('id', $array) || null == $array['id']) {
            return new $entity();
        }

        return $this->em->getRepository($entity)->findOneById($array['id']);
    }

    /**
     * @param Request $request
     * @param User    $user
     */
    public function validateAndCreatePollFromRequest(Request $request, $user)
    {
        /*
         * Validation du poll
         */
        if (($pollFromRequest = $request->get('poll')) !== null) {
            /** @var Poll $poll */
            $poll = $this->findIfExistOrCreateNew($pollFromRequest, Poll::class);
            $poll->setTitle($pollFromRequest['title']);
            $poll->setDescription($pollFromRequest['description']);
            $this->validatePollAndThrowIfErrors($poll);

            if ($poll->getUser() !== null) {
                $user = $poll->getUser();
            }

            /*
             * Validation des pages
             */
            if (isset($pollFromRequest['pages']) && ($pagesFromRequest = $pollFromRequest['pages']) !== null) {
                foreach ($pagesFromRequest as $pageFromRequest) {
                    /** @var Page $page */
                    $page = $this->findIfExistOrCreateNew($pageFromRequest, Page::class);
                    $page->setPoll($poll);
                    $page->setTitle($pageFromRequest['title']);
                    $page->setDescription($pageFromRequest['description']);
                    $this->validatePageAndThrowIfErrors($page);
                    $poll->addPage($page);

                    /**
                     * Validation des questions
                     */
                    if (isset($pageFromRequest['questions'])
                        && ($questionsFromRequest = $pageFromRequest['questions']) !== null
                    ) {
                        foreach ($questionsFromRequest as $questionFromRequest) {
                            /** @var Question $question */
                            $question = $this->findIfExistOrCreateNew($questionFromRequest, Question::class);
                            $question->setTitle($questionFromRequest['title']);
                            $question->setPage($page);
                            $question->setPoll($poll);
                            $this->validateQuestionAndThrowIfErrors($question);
                            $page->addQuestion($question);

                            // Chart Type
                            $chartType = $this->chartTypeRepositoryService->findOneBy([
                                'title' => $questionFromRequest['chart_type']['title']
                            ]);

                            if(is_null($chartType)) {
                                throw new ValidatorException($questionFromRequest['chart_type'], $question);
                            } else {
                                // TODO: Richard
                                $question->setChartType($chartType);
                            }

                            // Variant
                            $variant = $this->variantRepositoryService->getVariant([
                                'name' => $questionFromRequest['variant']['name'],
                            ]);

                            if (is_null($variant)) {
                                throw new ValidatorException($questionFromRequest['variant'], $question);
                            } else {
                                $this->validateVariantAndThrowIfErrors($variant);
                            }

                            if (isset($questionFromRequest['propositions'])
                                && ($propositionsFromRequest = $questionFromRequest['propositions']) !== null
                            ) {
                                foreach ($propositionsFromRequest as $propositionFromRequest) {
                                    /** @var Proposition $proposition */
                                    $proposition = $this->findIfExistOrCreateNew(
                                        $propositionFromRequest,
                                        Proposition::class
                                    );
                                    $proposition->setTitle($propositionFromRequest['title']);
                                    $proposition->setVariant($variant);
                                    $proposition->setQuestion($question);
                                    $this->validatePropositionAndThrowIfErrors($proposition);
                                    $question->addProposition($proposition);

                                    $this->pollCreationService->createPoll(
                                        $poll,
                                        $page,
                                        $question,
                                        $proposition,
                                        $user
                                    );
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

    /**
     * @param Poll $poll
     * @throws ValidationFailedException
     */
    public function validatePollAndThrowIfErrors(Poll $poll)
    {
        $errors = $this->validatePoll($poll);

        if (count($errors) > 0) {
            throw new ValidationFailedException($errors);
        }
    }

    /**
     * @param Poll $poll
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validatePoll(Poll $poll)
    {
        return $this->validator->validate($poll);
    }

    /**
     * @param Page $page
     * @throws ValidationFailedException
     */
    public function validatePageAndThrowIfErrors(Page $page)
    {
        $errors = $this->validatePage($page);

        if (count($errors) > 0) {
            throw new ValidationFailedException($errors);
        }
    }

    /**
     * @param Page $page
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validatePage(Page $page)
    {
        return $this->validator->validate($page);
    }

    /**
     * @param Question $question
     * @throws ValidationFailedException
     */
    public function validateQuestionAndThrowIfErrors(Question $question)
    {
        $errors = $this->validateQuestion($question);

        if (count($errors) > 0) {
            throw new ValidationFailedException($errors);
        }
    }

    /**
     * @param Question $question
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validateQuestion(Question $question)
    {
        return $this->validator->validate($question);
    }

    /**
     * @param Variant $variant
     * @throws ValidationFailedException
     */
    public function validateVariantAndThrowIfErrors(Variant $variant)
    {
        $errors = $this->validateVariant($variant);

        if (count($errors) > 0) {
            throw new ValidationFailedException($errors);
        }
    }

    /**
     * @param Variant $variant
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validateVariant($variant)
    {
        return $this->validator->validate($variant);
    }

    /**
     * @param Proposition $proposition
     * @throws ValidationFailedException
     */
    public function validatePropositionAndThrowIfErrors(Proposition $proposition)
    {
        $errors = $this->validateProposition($proposition);

        if (count($errors) > 0) {
            throw new ValidationFailedException($errors);
        }
    }

    /**
     * @param Proposition $proposition
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validateProposition(Proposition $proposition)
    {
        return $this->validator->validate($proposition);
    }
}
