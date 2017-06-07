<?php
/**
 * Created by PhpStorm.
 * User: kocal
 * Date: 13/05/17
 * Time: 08:09
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'users.first_name',
                'translation_domain' => 'AppBundle',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'users.last_name',
                'translation_domain' => 'AppBundle',
            ])
            ->remove('email')
            ->remove('username');
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_user_profile';
    }
}
