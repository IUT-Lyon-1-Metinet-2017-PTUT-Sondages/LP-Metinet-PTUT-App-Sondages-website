<?php

namespace AppBundle\Services;

use AppBundle\Entity\Poll;
use AppBundle\Entity\Question;
use AppBundle\Entity\Page;
use AppBundle\Entity\Proposition;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
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
    public $pollCreationService;
    public $em;


    /**
     * ValidationService constructor.
     * @param EntityManager                           $entityManager
     * @param Validator                               $validator
     * @param VariantRepositoryService                $variantRepositoryService
     * @param \AppBundle\Services\pollCreationService $pollCreationService
     */
    public function __construct(
        EntityManager $entityManager,
        Validator $validator,
        VariantRepositoryService $variantRepositoryService,
        PollCreationService $pollCreationService

    ) {
        $this->em                       = $entityManager;
        $this->validator                = $validator;
        $this->variantRepositoryService = $variantRepositoryService;
        $this->pollCreationService      = $pollCreationService;
    }

    public function findIfExistOrCreateNew($array, $entity)
    {

        if (!array_key_exists('id', $array) || null == $array['id']) {
            return new $entity();
        }

        return $this->em->getRepository('AppBundle:'.$entity)->findOneById($array['id']);
    }

    public function validateAndCreatePollFromRequest(Request $request, $user)
    {
        if (null !== $request->get('poll')) {
            /** @var Poll $poll */
            $poll = $this->findIfExistOrCreateNew($request->get('poll'), 'AppBundle\Entity\Poll');
            $poll->setTitle($request->get('poll')['title']);
            $poll->setDescription($request->get('poll')['description']);

            $pollErrors = $this->validatePoll($poll);

            if (count($pollErrors) > 0) {
                return $pollErrors;
            }
            if (null !== $request->get('poll')['pages'] && isset($request->get('poll')['pages'])) {
                foreach ($request->get('poll')['pages'] as $key => $page) {
                    /** @var Page $thisPage */
                    $thisPage = $this->findIfExistOrCreateNew($page, 'AppBundle\Entity\Page');
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
                            /** @var Question $thisQuestion */
                            $thisQuestion = $this->findIfExistOrCreateNew($question, 'AppBundle\Entity\Question');
                            $thisQuestion->setTitle($question['title']);


                            $questionErrors = $this->validateQuestion($question);

                            if (count($questionErrors) > 0) {
                                return $questionErrors;
                            }
                            $thisQuestion->setPage($thisPage);
                            $thisPage->addQuestion($thisQuestion);
                            $thisQuestion->setPoll($poll);
                            $poll->addQuestion($thisQuestion);


                            $variant = $this->variantRepositoryService->getVariant(['name' => $question['variant']]);
                            if (null !== $variant) {
                                $variantErrors = $this->validateVariant($variant);
                            } else {
                                return ['Variant does not exist '.implode(",", $question['variant']).'.. '];
                            }
                            if (count($variantErrors) > 0) {
                                return $variantErrors;
                            }

                            if (null !== $question['propositions'] && isset($question['propositions'])) {
                                foreach ($question['propositions'] as $proposition) {
                                    /** @var Proposition $thisProposition */
                                    $thisProposition = $this->findIfExistOrCreateNew(
                                        $proposition,
                                        'AppBundle\Entity\Proposition'
                                    );
                                    $thisProposition->setTitle($proposition['title']);
                                    $thisProposition->setVariant($variant);
                                    $thisProposition->setQuestion($thisQuestion);
                                    $thisQuestion->addProposition($thisProposition);

                                    $propositionErrors = $this->validateProposition($proposition);

                                    if (count($propositionErrors) > 0) {
                                        return $propositionErrors;
                                    }

                                    $this->pollCreationService->createPoll(
                                        $poll,
                                        $thisPage,
                                        $thisQuestion,
                                        $thisProposition,
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
     * @param $poll
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validatePoll($poll)
    {
        $errors = $this->validator->validate($poll);

        return $errors;
    }

    /**
     * @param $page
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validatePage($page)
    {
        $errors = $this->validator->validate($page);

        return $errors;
    }

    /**
     * @param $variant
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validateVariant($variant)
    {
        $errors = $this->validator->validate($variant);

        return $errors;
    }

    /**
     * @param $question
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validateQuestion($question)
    {
        $errors = $this->validator->validate($question);

        return $errors;
    }

    /**
     * @param $proposition
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validateProposition($proposition)
    {
        $errors = $this->validator->validate($proposition);

        return $errors;
    }

    /**
     * @param $answer
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validateAnswer($answer)
    {
        $errors = $this->validator->validate($answer);

        return $errors;
    }
}
