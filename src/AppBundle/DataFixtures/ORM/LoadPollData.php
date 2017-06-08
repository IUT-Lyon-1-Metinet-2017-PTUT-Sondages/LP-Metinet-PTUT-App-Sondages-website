<?php

namespace App\DataFixtures\ORM;

use AppBundle\Entity\ChartType;
use AppBundle\Entity\Page;
use AppBundle\Entity\Poll;
use AppBundle\Entity\Proposition;
use AppBundle\Entity\Question;
use AppBundle\Entity\User;
use AppBundle\Entity\Variant;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadPollData
 * @package App\DataFixtures\ORM
 */
class LoadPollData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    private $fixturePolls = [
        [
            'user' => 0,
            'title' => 'Retour sur la formation LP : Metinet',
            'description' => 'Suite à votre licence professionnelle, merci de répondre à se sondage de satisfaction.',
            'pages' => [
                [
                    'title' => 'Cours',
                    'description' => 'Questions portant sur les cours de la LP METINET.',
                    'questions' => [
                        [
                            'title' => 'Sur une échelle de 1 à 10, les cours reçus au sein de la formation sont-ils pertinents ?',
                            'variant_type' => Variant::LINEAR_SCALE,
                            'chart_type' => ChartType::PIE,
                            'propositions' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                        ],
                        [
                            'title' => 'Votre alternance était-elle en adéquation avec la formation ?',
                            'variant_type' => Variant::RADIO,
                            'chart_type' => ChartType::BAR,
                            'propositions' => [
                                'Oui, tout à fait',
                                'Plus ou moins',
                                'Non pas du tout'
                            ]
                        ],
                        [
                            'title' => 'Sélectionnez les cours qui vont ont plu',
                            'variant_type' => Variant::CHECKBOX,
                            'chart_type' => ChartType::PIE,
                            'propositions' => [
                                'UML',
                                'Java',
                                'Android',
                                '.NET/ASP.NET',
                                'Graphisme',
                                'HTML/CSS',
                                'JavaScript/Angular',
                                'PHP/Symfony',
                                'J2EE',
                                'Référencements/Accessibilité',
                            ]
                        ],
                    ]
                ],
                [
                    'title' => 'IUT',
                    'description' => 'Questions à propos de l\'IUT en général.',
                    'questions' => [
                        [
                            'title' => 'Quel est votre avis sur le restaurant universitaire ?',
                            'variant_type' => Variant::RADIO,
                            'chart_type' => ChartType::BAR,
                            'propositions' => [
                                'Très satisfait',
                                'Moyennement satisfait',
                                'Peu satisfait',
                                'Pas du tout satisfait',
                            ]
                        ],
                        [
                            'title' => 'Quel est votre avis sur la journée d\'intégration ?',
                            'variant_type' => Variant::RADIO,
                            'chart_type' => ChartType::PIE,
                            'propositions' => [
                                'Très satisfait',
                                'Moyennement satisfait',
                                'Peu satisfait',
                                'Pas du tout satisfait',
                            ]
                        ],
                        [
                            'title' => 'Sur une échelle de 1 à 10 quel est votre avis sur les infrastructures ?',
                            'chart_type' => ChartType::BAR,
                            'variant_type' => Variant::LINEAR_SCALE,
                            'propositions' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                        ],
                        [
                            'title' => 'Sur une échelle de 1 à 10 quel est votre avis sur le matériel informatique ?',
                            'chart_type' => ChartType::PIE,
                            'variant_type' => Variant::LINEAR_SCALE,
                            'propositions' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                        ],
                    ]
                ]
            ]
        ],
        [
            'user' => 0,
            'title' => 'Votre avis sur cette interface',
            'description' => 'Suite à l\'utilisation de cette interface merci de répondre à ce sondage.',
            'pages' => [
                [
                    'title' => 'Fonctionnalités',
                    'description' => 'Questions à propos des fonctionnalités de l\'interface.',
                    'questions' => [
                        [
                            'title' => 'Qu\'avez-vous pensé des fonctionnalités ?',
                            'variant_type' => Variant::RADIO,
                            'chart_type' => ChartType::BAR,
                            'propositions' => [
                                'Très bien',
                                'Pas mal',
                                'Plutôt bien',
                            ]
                        ],
                        [
                            'title' => 'Avez-vous remarqué des erreurs/bugs dans l\'interface ?',
                            'variant_type' => Variant::RADIO,
                            'chart_type' => ChartType::PIE,
                            'propositions' => [
                                'Oui',
                                'Non',
                            ]
                        ],
                    ]
                ],
                [
                    'title' => 'Expérience utilisateur',
                    'description' => 'Questions à propos de l\'expérience utilisateur de l\'interface.',
                    'questions' => [
                        [
                            'title' => 'Sur une échelle de 1 à 5 quel est votre avis sur la fluidité de l\'application ?',
                            'chart_type' => ChartType::BAR,
                            'variant_type' => Variant::LINEAR_SCALE,
                            'propositions' => [1, 2, 3, 4, 5],
                        ],
                        [
                            'title' => 'Sur une échelle de 1 à 5 quel est votre avis sur la clareté des informations ?',
                            'chart_type' => ChartType::PIE,
                            'variant_type' => Variant::LINEAR_SCALE,
                            'propositions' => [1, 2, 3, 4, 5],
                        ],
                        [
                            'title' => 'Avez-vous remarqué des erreurs/bugs dans l\'interface ?',
                            'variant_type' => Variant::RADIO,
                            'chart_type' => ChartType::BAR,
                            'propositions' => [
                                'Simple et efficace',
                                'A revoir',
                                'Trop neutre',
                                'Trop coloré',
                            ]
                        ],
                    ]
                ]
            ]
        ],
        [
            'user' => 1,
            'title' => 'Les objets connectés',
            'description' => 'Nous vous remercions de répondre aux questions qui vont suivre en cochant la ou les réponses correspondant à votre point de vue. Il n’y a ni bonne ni mauvaise réponse, seul votre avis nous intéresse !',
            'pages' => [
                [
                    'title' => 'Les objets connectés',
                    'description' => 'Des questions à propos des objets connectés',
                    'questions' => [
                        [
                            'title' => 'Possédez-vous déjà un ou plusieurs objets connectés de santé ?',
                            'variant_type' => Variant::RADIO,
                            'chart_type' => ChartType::PIE,
                            'propositions' => [
                                "Oui, depuis longtemps",
                                "Oui, depuis peu",
                                "Non, mais j'envisage d'en acquérir",
                                "Non, et cela ne m'intéresse pas",
                            ]
                        ],
                        [
                            'title' => 'Globalement, êtes-vous satisfait(e) de cet/ces objet(s) connecté(s) de santé ?',
                            'variant_type' => Variant::LINEAR_SCALE,
                            'chart_type' => ChartType::BAR,
                            'propositions' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                        ],
                        [
                            'title' => 'A quelle fréquence utilisez-vous cet objet connecté de santé ?',
                            'variant_type' => Variant::RADIO,
                            'chart_type' => ChartType::PIE,
                            'propositions' => [
                                'Plusieurs fois par jour',
                                '1 fois par jour',
                                '2 à 3 fois par semaine',
                                '1 fois par mois',
                                'Plus occasionnellement',
                            ]
                        ],
                        [
                            'title' => 'Parmi les objets connectés de santé de la liste ci-dessous, le(s)quel(s) possédez-vous ?',
                            'variant_type' => Variant::CHECKBOX,
                            'chart_type' => ChartType::BAR,
                            'propositions' => [
                                'Un bracelet connecté',
                                'Une montre connectée',
                                'Un application mobile de santé',
                                'Un balance connectée',
                                'Un thermomètre connectée',
                                'Un tensiomètre connectée',
                                'Un glucomètre connectée',
                            ]
                        ],
                    ]
                ]
            ]
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $userRepository = $this->manager->getRepository('AppBundle:User');
        $variantRepository = $this->manager->getRepository('AppBundle:Variant');
        $chartTypeRepository = $this->manager->getRepository('AppBundle:ChartType');

        foreach ($this->fixturePolls as $fixturePoll) {
            /** @var User $user */
            $user = $userRepository->findAll()[$fixturePoll['user']];
            $poll = new Poll();
            $poll->setTitle($fixturePoll['title']);
            $poll->setDescription($fixturePoll['description']);
            $poll->setUser($user);
            $user->addPoll($poll);

            foreach ($fixturePoll['pages'] as $fixturePage) {
                $page = new Page();
                $page->setTitle($fixturePage['title']);
                $page->setDescription($fixturePage['description']);
                $page->setPoll($poll);
                $poll->addPage($page);

                foreach ($fixturePage['questions'] as $fixtureQuestion) {
                    $question = new Question();
                    $question->setTitle($fixtureQuestion['title']);
                    $question->setPoll($poll);
                    $question->setPage($page);
                    $question->setChartType($chartTypeRepository->findOneBy(['title' => $fixtureQuestion['chart_type']]));
                    $page->addQuestion($question);

                    foreach ($fixtureQuestion['propositions'] as $fixtureProposition) {
                        $proposition = new Proposition();
                        $proposition->setTitle($fixtureProposition);
                        $proposition->setVariant($variantRepository->findOneBy(['name' => $fixtureQuestion['variant_type']]));
                        $proposition->setQuestion($question);
                        $question->addProposition($proposition);
                    }

                    $this->manager->persist($question);
                }

                $this->manager->persist($page);
            }

            $this->manager->persist($poll);
            $this->manager->persist($user);
        }

        $this->manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
