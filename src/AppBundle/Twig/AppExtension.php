<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Variant;
use AppBundle\Helper;
use AppBundle\Services\VariantRepositoryService;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AppExtension extends \Twig_Extension
{
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var VariantRepositoryService
     */
    private $variantRepositoryService;

    /**
     * AppExtension constructor.
     * @param Helper $helper
     * @param Translator $translator
     * @param VariantRepositoryService $variantRepositoryService
     */
    public function __construct(
        Helper $helper,
        Translator $translator,
        VariantRepositoryService $variantRepositoryService
    )
    {
        $this->helper = $helper;
        $this->translator = $translator;
        $this->variantRepositoryService = $variantRepositoryService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'loadTranslationsForVueJS',
                [$this, 'loadTranslationsForVueJSFunction'],
                [
                    'is_safe' => ['html'],
                ]
            ),
            new \Twig_SimpleFunction(
                'loadVariantsFromDatabase',
                [$this, 'loadVariantsFromDatabaseFunction'],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    /**
     * @param string $domain
     * @param string $path
     * @return string
     */
    public function loadTranslationsForVueJSFunction($domain, $path = '*')
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $locale = $this->translator->getLocale();

        $translations = $this->helper->loadTranslations($domain, $locale);
        $translations = $accessor->getValue($translations, $path);
        $translations = json_encode($translations);

        return <<<HEREDOC
<script>
    var LOCALE = "{$locale}";
    var TRANSLATIONS = {$translations};
</script>
HEREDOC;
    }

    /**
     * @return string
     */
    public function loadVariantsFromDatabaseFunction()
    {
        $variants = $this->variantRepositoryService->getVariants();
        $variants = array_map(function (Variant $variant) {
            return [
                'id' => $variant->getId(),
                'title' => $variant->getName(),
            ];
        }, $variants);
        $variants = array_column($variants, 'title', 'id');
        $variants = json_encode($variants);

        return <<<HEREDOC
<script>
    var VARIANTS = {$variants};
</script>
HEREDOC;
    }
}
