<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
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
                'label' => 'form.first_name',
                'translation_domain' => 'FOSUserBundle',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.last_name',
                'translation_domain' => 'FOSUserBundle',
            ])
            ->remove('email')
            ->remove('plainPassword')
            ->add('submit', SubmitType::class, [
                'label' => 'profile.edit.submit',
                'translation_domain' => 'FOSUserBundle',
                'attr' => [
                    'class' => 'btn btn-primary btn-lg',
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return RegistrationType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_user_update';
    }
}
