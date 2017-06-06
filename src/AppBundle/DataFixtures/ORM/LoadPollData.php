<?php

namespace App\DataFixtures\ORM;

use AppBundle\Entity\Page;
use AppBundle\Entity\Poll;
use AppBundle\Entity\Proposition;
use AppBundle\Entity\Question;
use AppBundle\Entity\User;
use AppBundle\Entity\Variant;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadPollData
 * @package App\DataFixtures\ORM
 */
class LoadPollData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $users = $this->manager->getRepository('AppBundle:User')->findAll();

        foreach ($users as $user) {
            $this->createPollsForUser($user);
        }

        $this->manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * @param User $user
     */
    private function createPollsForUser(User $user)
    {
        for ($i = 0; $i < 2; $i++) {
            $poll = new Poll();
            $poll->setTitle(sprintf('Sondage n°%d de %s', $i, $user->getFirstName()));
            $poll->setDescription("La description du sondage");
            $poll->setUser($user);
            $user->addPoll($poll);

            $this->createPagesForPoll($poll);

            $this->manager->persist($poll);
            $this->manager->persist($user);
        }
    }

    /**
     * @param Poll $poll
     */
    private function createPagesForPoll(Poll $poll)
    {
        for ($i = 0; $i < 2; $i++) {
            $page = new Page();
            $page->setTitle(sprintf("La page n°%d", $i));
            $page->setDescription(sprintf("La description de la page n°%d", $i));
            $page->setPoll($poll);
            $poll->addPage($page);
            $this->createQuestionsForPage($poll, $page);

            $this->manager->persist($page);
            $this->manager->persist($poll);
        }
    }

    /**
     * @param Poll $poll
     * @param Page $page
     */
    private function createQuestionsForPage(Poll $poll, Page $page)
    {
        $variants = $this->manager->getRepository('AppBundle:Variant')->findAll();
        $chartTypes = $this->manager->getRepository('AppBundle:ChartType')->findAll();

        foreach ($variants as $variant) {
            $question = new Question();
            $question->setTitle(sprintf("Question de type %s", $variant->getName()));
            $question->setPoll($poll);
            $question->setPage($page);
            $question->setChartType($chartTypes[array_rand($chartTypes)]);
            $page->addQuestion($question);
            $this->createPropositionsForQuestion($question, $variant);

            $this->manager->persist($question);
        }
    }

    /**
     * @param Question $question
     * @param Variant  $variant
     */
    private function createPropositionsForQuestion(Question $question, Variant $variant)
    {
        for ($i = 0; $i < 5; $i++) {
            $proposition = new Proposition();
            $proposition->setTitle(sprintf("Proposition n°%d", $i));

            if ($variant->getName() === 'LinearScale') {
                $proposition->setTitle($i);
            }

            $proposition->setQuestion($question);
            $proposition->setVariant($variant);
            $question->addProposition($proposition);

            $this->manager->persist($proposition);
            $this->manager->persist($question);
        }
    }
}
