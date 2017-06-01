<?php
/**
 * Created by PhpStorm.
 * User: richard
 * Date: 07/04/17
 * Time: 11:38
 */

namespace AppBundle\Filters;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeleteableFilter extends SQLFilter
{
    protected $entityManager;
    protected $disabled = array();

    /**
     * Add the soft deletable filter
     *
     * @param  ClassMetaData $targetEntity
     * @param                $targetTableAlias
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $class = $targetEntity->getName();
        if (array_key_exists($class, $this->disabled) && $this->disabled[$class] === true) {
            return '';
        } elseif (
            array_key_exists($targetEntity->rootEntityName, $this->disabled)
            && $this->disabled[$targetEntity->rootEntityName] === true
        ) {
            return '';
        } elseif (!$targetEntity->hasField('deletedAt')) {
            return '';
        }
        $conn = $this->getEntityManager()->getConnection();
        $platform = $conn->getDatabasePlatform();
        $column = $targetEntity->getQuotedColumnName('deletedAt', $platform);
        $addCondSql = $platform->getIsNullExpression($targetTableAlias . '.' . $column);
        return $addCondSql;
    }

    protected function getEntityManager()
    {
        if ($this->entityManager === null) {
            $refl = new \ReflectionProperty('Doctrine\ORM\Query\Filter\SQLFilter', 'em');
            $refl->setAccessible(true);
            $this->entityManager = $refl->getValue($this);
        }
        return $this->entityManager;
    }

    public function disableForEntity($class)
    {
        $this->disabled[$class] = true;
    }

    public function enableForEntity($class)
    {
        $this->disabled[$class] = false;
    }
}