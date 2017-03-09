<?php

namespace Tests\AppBundle;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Created by PhpStorm.
 * User: kocal
 * Date: 09/03/17
 * Time: 14:19
 */
class DomainTestCase extends WebTestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    protected function setUp()
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $this->runCommand('doctrine:schema:create', [], true);
        $this->runCommand('doctrine:schema:validate', [], true);
    }

    protected function tearDown()
    {
        $this->runCommand('doctrine:schema:drop', ['--force' => true], true);
    }

    protected function quote($msg)
    {
        return htmlspecialchars($msg, ENT_QUOTES);
    }
}