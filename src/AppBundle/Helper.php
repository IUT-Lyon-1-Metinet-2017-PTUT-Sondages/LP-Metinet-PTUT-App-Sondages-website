<?php

namespace AppBundle;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class Helper
{
    /**
     * @param string $domain
     * @param string $locale
     * @return array
     */
    public function loadTranslations($domain, $locale)
    {
        $configDirectories = [
            __DIR__ . '/Resources/translations',
        ];

        $locator = new FileLocator($configDirectories);

        $translationsFile = $locator->locate(
            sprintf('%s.%s.yml', $domain, $locale),
            null,
            true
        );

        return Yaml::parse(file_get_contents($translationsFile));
    }

    /**
     * @param string $value
     * @return string
     */
    public function replaceSymfonyFormattingTagsForVueI18n($value)
    {
        return preg_replace('#%([^%]+)%#', '{$1}', $value);
    }
}
