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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('post')
            ->add('firstName', TextType::class, ['label' => 'user.first_name', 'translation_domain' => 'form'])
            ->add('lastName', TextType::class, ['label' => 'user.last_name', 'translation_domain' => 'form'])
            ->remove('email')
            ->remove('plainPassword')
            ->add('Modifier', SubmitType::class, ['attr' => ['class' => 'btn btn-primary']]);
    }

    public function getParent()
    {
        return RegistrationType::class;
    }

    public function getBlockPrefix()
    {
        return 'app_user_update';
    }
}