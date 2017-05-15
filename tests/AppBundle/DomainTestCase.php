<?php

namespace Tests\AppBundle;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DomainTestCase extends WebTestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $this->runCommand('doctrine:schema:create', [], true);
        $this->runCommand('doctrine:schema:validate', [], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->runCommand('doctrine:schema:drop', ['--force' => true], true);
    }

    /**
     * @param string $msg
     * @return string
     */
    protected function quote($msg)
    {
        return htmlspecialchars($msg, ENT_QUOTES);
    }
}