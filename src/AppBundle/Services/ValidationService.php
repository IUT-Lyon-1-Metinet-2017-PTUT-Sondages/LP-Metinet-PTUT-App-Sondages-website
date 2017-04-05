<?php

namespace AppBundle\Services;

use AppBundle\Entity\Poll;
use AppBundle\Entity\Question;
use AppBundle\Entity\Variant;
use AppBundle\Entity\Page;
use AppBundle\Entity\Proposition;


use AppBundle\Services\VariantRepositoryService;


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

    function __construct(Validator $validator, VariantRepositoryService $variantRepositoryService)
    {
        $this->validator = $validator;
        $this->variantRepositoryService = $variantRepositoryService;
    }

    public function validatePollRequest($request){
        $poll = new Poll;
        $poll->setTitle($request->get('poll')['title']);
        $poll->setDescription($request->get('poll')['description']);

        $pollErrors = $this->validatePoll($poll);

        if(count($pollErrors) > 0){
            return $pollErrors;
        }

        foreach ($request->get('poll')['pages'] as $key => $page){
            $thisPage = new Page;
            $thisPage->setTitle($page['title']);
            // $thisPage->setDescription($page['description']); // TODO : No description for now, implement it
            $pageErrors = $this->validatePage($thisPage);

            if(count($pageErrors) > 0){
                return $pageErrors;
            }
            $thisPage->setPoll($poll);
            $poll->addPage($thisPage);
            foreach ($page['questions'] as $question){
                $thisQuestion = new Question;
                $thisQuestion->setTitle($question['question']['title']);


                $questionErrors = $this->validateQuestion($question);

                if(count($questionErrors) > 0 ){
                    return $questionErrors;
                }
                $thisQuestion->setPage($thisPage);
                $thisPage->addQuestion($thisQuestion);
                $thisQuestion->setPoll($poll);
                $poll->addQuestion($thisQuestion);

                //$variantErrors = $this->validateVariant($question['variant']); // TODO: Implement variant validation

                /*
                 *  if(count($variantErrors) > 0){ return $variantErrors;  }
                 *  $variant = $this->variantRepositoryService->getVariant(['id'=>$question['variant']]);
                 *  TODO:  implement variant query like above
                 */

                foreach ($question['propositions'] as $proposition){
                    $thisProposition = new Proposition();
                    $thisProposition->setTitle($proposition['title']);
                    //$thisProposition->setVariant($variant); // see above
                    $thisProposition->setQuestion($thisQuestion);
                    $thisQuestion->addProposition($thisProposition);

                    $propositionErrors = $this->validateProposition($proposition);

                    if(count($propositionErrors) > 0){
                        return $propositionErrors;
                    }


                }

            }

        }


    }

    public function validatePoll($poll){
        $errors = $this->validator->validate($poll);
        return $errors;
    }

    public function validatePage($page){
        $errors = $this->validator->validate($page);

        return $errors;
    }

    public function validateVariant($variant){
        $errors = $this->validator->validate($variant);
        return $errors;
    }

    public function validateQuestion($question){
        $errors = $this->validator->validate($question);

        return $errors;
    }

    public function validateProposition($proposition){
        $errors = $this->validator->validate($proposition);

        return $errors;
    }

    public function validateAnswer($answer){
        $errors = $this->validator->validate($answer);

        return $errors;
    }

}
