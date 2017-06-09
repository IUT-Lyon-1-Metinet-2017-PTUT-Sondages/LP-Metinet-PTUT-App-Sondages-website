<?php

namespace AppBundle\Form;

use AppBundle\Entity\ValidDomain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ConfigType
 * @package AppBundle\Form
 */
class ConfigType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain', TextType::class, [
                'label' => 'config.edit.mail_regexp',
                'translation_domain' => 'AppBundle',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'config.edit.submit',
                'translation_domain' => 'AppBundle',
                'attr' => [
                    'class' => 'btn btn-primary btn-lg',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ValidDomain::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_config';
    }
}
