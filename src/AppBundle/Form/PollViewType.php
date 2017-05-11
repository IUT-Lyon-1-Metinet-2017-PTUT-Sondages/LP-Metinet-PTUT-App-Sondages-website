<?php

namespace AppBundle\Form;

use AppBundle\Entity\Poll;
use AppBundle\Entity\Proposition;
use AppBundle\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PollViewType
 * @package AppBundle\Form
 */
class PollViewType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Poll $poll */
        $poll = $options['poll'];

        /** @var Question $question */
        foreach ($poll->getQuestions() as $question) {
            $choices = [];
            $variant = null;
            /** @var Proposition $proposition */
            foreach ($question->getPropositions() as $proposition) {
                $variant = $proposition->getVariant()->getId();
                $choices[$proposition->getTitle()] = $proposition->getId();
            }
            $builder->add('question' . $question->getId(), ChoiceType::class, [
                'choices'  => $choices,
                'expanded' => $this->convertVariantToInputType($variant) == 2,
                'multiple' => !$this->convertVariantToInputType($variant),
                'label'    => $question->getTitle()
            ]);
        }

        $builder->add('submit_poll', SubmitType::class, [
            'label' => 'Envoyer',
            'attr' => ['class' => 'btn btn-send col-xs-12 float-right']
        ]);
    }

    // TODO : faire un helper qui renvoit les différentes cases (int) en allant les chercher en BDD
    private function convertVariantToInputType(int $variant)
    {
        switch ($variant) {
            case 2:
                return 0;
                break;
            case 1:
                return 1;
                break;
            case 3:
                return 2;
                break;
            default:
                return 0;
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'poll' => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_poll_view';
    }
}
