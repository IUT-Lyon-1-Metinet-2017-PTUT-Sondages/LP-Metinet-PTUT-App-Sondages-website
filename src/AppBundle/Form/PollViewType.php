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
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Required;

/**
 * Class PollViewType
 * @package AppBundle\Form
 */
class PollViewType extends AbstractType
{
    /**
     * @var VariantRepositoryService
     */
    private $variantRepositoryService;

    /**
     * PollViewType constructor.
     * @param VariantRepositoryService $variantRepositoryService
     */
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
        /** @var boolean $currentUserHasCreatedThisPoll */
        $currentUserHasCreatedThisPoll = $options['currentUserHasCreatedThisPoll'];

        $shouldDisableInputs = $currentUserHasCreatedThisPoll && !$poll->isPublished();

        /** @var Question $question */
        foreach ($poll->getQuestions() as $question) {
            $choices = [];
            $variantId = null;

            /** @var Proposition $proposition */
            foreach ($question->getPropositions() as $proposition) {
                $variantId = $proposition->getVariant()->getId();
                $choices[$proposition->getTitle()] = $proposition->getId();
            }

            $inputType = $this->convertVariantToInputType($variantId);
            $isCheckbox = $inputType === 0;
            $constraints = [
                new NotBlank()
            ];

            if($isCheckbox) {
                $constraints[] = new Count(['min' => 1]);
            }

            $builder->add('question' . $question->getId(), ChoiceType::class, [
                'choices' => $choices,
                'expanded' => $inputType == 2,
                'multiple' => $isCheckbox,
                'label' => $question->getTitle(),
                'disabled' => $shouldDisableInputs,
                'constraints' => $constraints
            ]);
        }

        $builder->add('submit_poll', SubmitType::class, [
            'label' => 'poll.answer_to_poll',
            'translation_domain' => 'AppBundle',
            'attr' => ['class' => 'btn btn-lg btn-primary'],
            'disabled' => $shouldDisableInputs,
        ]);
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

    /**
     * @param int $variantId
     * @return int
     */
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
}
