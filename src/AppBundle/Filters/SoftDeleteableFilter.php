<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 07/04/17
 * Time: 11:38
 */

namespace AppBundle\Filters;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeleteableFilter extends SQLFilter
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    protected $disabled = [];

    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $class = $targetEntity->getName();

        if (array_key_exists($class, $this->disabled) && $this->disabled[$class] === true) {
            return '';
        } elseif (array_key_exists($targetEntity->rootEntityName, $this->disabled)
            && $this->disabled[$targetEntity->rootEntityName] === true
        ) {
            return '';
        } elseif (!$targetEntity->hasField('deletedAt')) {
            return '';
        }

        $connection = $this->getEntityManager()->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();
        $column = $targetEntity->getQuotedColumnName('deletedAt', $databasePlatform);
        $addCondSql = $databasePlatform->getIsNullExpression($targetTableAlias . '.' . $column);

        return $addCondSql;
    }

    /**
     * @param mixed $class
     */
    public function disableForEntity($class)
    {
        $this->disabled[$class] = true;
    }

    /**
     * @param mixed $class
     */
    public function enableForEntity($class)
    {
        $this->disabled[$class] = false;
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        if ($this->entityManager === null) {
            $refl = new \ReflectionProperty('Doctrine\ORM\Query\Filter\SQLFilter', 'em');
            $refl->setAccessible(true);
            $this->entityManager = $refl->getValue($this);
        }

        return $this->entityManager;
    }
}
