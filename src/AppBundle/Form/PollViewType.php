<?php

namespace AppBundle\Form;

use AppBundle\Entity\Poll;
use AppBundle\Entity\Proposition;
use AppBundle\Entity\Question;
use AppBundle\Services\VariantRepositoryService;
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
    private $variantRepositoryService;

    public function __construct(VariantRepositoryService $variantRepositoryService)
    {
        $this->variantRepositoryService = $variantRepositoryService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Poll $poll */
        $poll = $options['poll'];
        $currentUserHasCreatedThisPoll = $options['currentUserHasCreatedThisPoll'];

        /** @var Question $question */
        foreach ($poll->getQuestions() as $question) {
            $choices = [];
            $variantId = null;
            /** @var Proposition $proposition */
            foreach ($question->getPropositions() as $proposition) {
                $variantId = $proposition->getVariant()->getId();
                $choices[$proposition->getTitle()] = $proposition->getId();
            }
            $builder->add('question' . $question->getId(), ChoiceType::class, [
                'choices' => $choices,
                'expanded' => $this->convertVariantToInputType($variantId) == 2,
                'multiple' => !$this->convertVariantToInputType($variantId),
                'label' => $question->getTitle(),
                'disabled' => !$poll->isPublished() && $currentUserHasCreatedThisPoll,
            ]);
        }

        $builder->add('submit_poll', SubmitType::class, [
            'label' => 'Envoyer',
            'attr' => ['class' => 'btn btn-send col-xs-12 float-right'],
            'disabled' => !$poll->isPublished() && $currentUserHasCreatedThisPoll
        ]);
    }

    private function convertVariantToInputType(int $variantId)
    {
        switch ($variantId) {
            case $this->variantRepositoryService->getCheckboxType()->getId():
                return 0;
                break;
            case $this->variantRepositoryService->getRadioType()->getId():
                return 1;
                break;
            case $this->variantRepositoryService->getLinearScaleType()->getId():
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
            'poll' => null,
            'currentUserHasCreatedThisPoll' => false,
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
