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
    /**
     * @var PropositionRepositoryService
     */
    private $propositionRepositoryService;

    /**
     * @var AnswerRepositoryService
     */
    private $answerRepositoryService;

    /**
     * PollAnswersService constructor.
     * @param PropositionRepositoryService $propositionRepositoryService
     * @param AnswerRepositoryService      $answerRepositoryService
     */
    public function __construct(
        PropositionRepositoryService $propositionRepositoryService,
        AnswerRepositoryService $answerRepositoryService
    ) {
        $this->propositionRepositoryService = $propositionRepositoryService;
        $this->answerRepositoryService = $answerRepositoryService;
    }

    /**
     * @param Poll  $poll
     * @param array $data
     */
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

    /**
     * @param int    $propositionId
     * @param string $sessionId
     */
    private function saveAnswer($propositionId, $sessionId)
    {
        $proposition = $this->propositionRepositoryService->getProposition(['id' => $propositionId]);
        $answer = new Answer();
        $answer->setProposition($proposition);
        $answer->setSessionId($sessionId);
        $this->answerRepositoryService->createAnswer($answer);
    }
}
