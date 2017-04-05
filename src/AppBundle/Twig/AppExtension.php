<?php
namespace AppBundle\Twig;

use AppBundle\Helper;
use Symfony\Component\Translation\DataCollectorTranslator;

class AppExtension extends \Twig_Extension
{
    private $helper;
    private $translator;

    public function __construct(Helper $helper, DataCollectorTranslator $translator)
    {
        $this->helper = $helper;
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('loadTranslationsForVueJS', [$this, 'loadTranslationsForVueJSFunction'], [
                'is_safe' => ['html']
            ]),
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
}