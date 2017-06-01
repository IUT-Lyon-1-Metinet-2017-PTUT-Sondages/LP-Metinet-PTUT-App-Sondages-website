<?php
namespace AppBundle\Twig;

use AppBundle\Entity\Variant;
use AppBundle\Helper;
use AppBundle\Services\VariantRepositoryService;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class AppExtension extends \Twig_Extension
{
    private $helper;
    private $translator;
    private $variantRepositoryService;

    public function __construct(Helper $helper, Translator $translator, VariantRepositoryService $variantRepositoryService)
    {
        $this->helper = $helper;
        $this->translator = $translator;
        $this->variantRepositoryService = $variantRepositoryService;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('loadTranslationsForVueJS', [$this, 'loadTranslationsForVueJSFunction'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('loadVariantsFromDatabase', [$this, 'loadVariantsFromDatabaseFunction'], ['is_safe' => ['html']]),
        ];
    }

    public function loadTranslationsForVueJSFunction($domain)
    {
        $locale = $this->translator->getLocale();
        $translations = $this->helper->loadTranslations($domain, $locale);
        $translations = json_encode($translations);

        return <<<HEREDOC
<script>
    var LOCALE = "{$locale}";
    var TRANSLATIONS = {$translations};
</script>
HEREDOC;
    }

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