<?php
/**
 * Created by IntelliJ IDEA.
 * User: kocal
 * Date: 29/05/17
 * Time: 16:31
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileDeleteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'form.password',
                'translation_domain' => 'FOSUserBundle',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('confirmation', CheckboxType::class, [
                'label' => 'form.delete_account_confirmation',
                'translation_domain' => 'FOSUserBundle',
                'constraints' => [
                    new IsTrue(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.delete_account',
                'translation_domain' => 'FOSUserBundle',
                'attr' => [
                    'class' => 'btn btn-danger btn'
                ]
            ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_user_profile_delete';
    }
}

