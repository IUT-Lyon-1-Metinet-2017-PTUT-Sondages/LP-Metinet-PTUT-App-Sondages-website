<?php

namespace AppBundle\Services;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Poll;

/**
 * Class PollAnswersService
 * @package AppBundle\Services
 */
class PollAnswersService
{
    private $propositionRepositoryService;

    private $answerRepositoryService;

    public function __construct(
        PropositionRepositoryService $propositionRepositoryService,
        AnswerRepositoryService $answerRepositoryService
    ) {
        $this->propositionRepositoryService = $propositionRepositoryService;
        $this->answerRepositoryService      = $answerRepositoryService;
    }

    public function registerAnswers(Poll $poll, array $data)
    {
        $sessionId = uniqid();
        foreach ($data as $propositions) {
            if (is_array($propositions)) {
                foreach ($propositions as $propositionId) {
                    $this->saveAnswer($propositionId, $sessionId);
                }
            } else {
                $this->saveAnswer($propositions, $sessionId);
            }
        }
    }

    private function saveAnswer($propositionId, $sessionId)
    {
        $answer = new Answer();
        $proposition = $this->propositionRepositoryService->getProposition(['id' => $propositionId]);
        $answer->setProposition($proposition);
        $answer->setSessionId($sessionId);
        $this->answerRepositoryService->createAnswer($answer);
    }
}
