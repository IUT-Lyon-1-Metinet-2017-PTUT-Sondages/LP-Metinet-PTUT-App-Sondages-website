<?php
namespace AppBundle\Twig;

use AppBundle\Entity\Variant;
use AppBundle\Helper;
use AppBundle\Services\VariantRepositoryService;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\Translation\DataCollectorTranslator;

class AppExtension extends \Twig_Extension
{
    private $helper;
    private $translator;
    private $variantRepositoryService;

    public function __construct(Helper $helper, DataCollectorTranslator $translator, VariantRepositoryService $variantRepositoryService)
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
        $translations = json_encode(
            $this->helper->unflatten(
                array_map(
                    [$this->helper, 'replaceSymfonyFormattingTagsForVueI18n'],
                    $this->translator->getCatalogue()->all($domain)
                )
            )
        );

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
                'id' => $variant->getTitle(),
                'title' => Inflector::classify(strtolower($variant->getTitle()))
                // LINEAR_SCALE => LinearScale, RADIO => Radio, ...
                //  ^ key          ^ humanized
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