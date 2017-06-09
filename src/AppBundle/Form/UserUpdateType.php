<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class RegistrationType
 * @package AppBundle\Form
 */
class UserUpdateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('post')
            ->add('firstName', TextType::class, [
                'label' => 'users.first_name',
                'translation_domain' => 'AppBundle',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'users.last_name',
                'translation_domain' => 'AppBundle',
            ])
            ->add('email', EmailType::class, [
                'label' => 'users.email',
                'translation_domain' => 'AppBundle',
            ])
            ->remove('plainPassword')
            ->add('submit', SubmitType::class, [
                'label' => 'profile.edit.submit',
                'translation_domain' => 'FOSUserBundle',
                'attr' => [
                    'class' => 'btn btn-primary btn-lg',
                ],
            ]);
    }
}
