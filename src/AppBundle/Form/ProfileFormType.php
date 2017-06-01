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
                'label' => 'user.first_name',
                'translation_domain' => 'FOSUserBundle',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'user.last_name',
                'translation_domain' => 'FOSUserBundle',
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
