<?php
/**
 * Created by PhpStorm.
 * User: kocal
 * Date: 04/04/17
 * Time: 22:16
 */

namespace AppBundle;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class Helper
{
    public function loadTranslations($domain, $locale)
    {
        $configDirectories = [
            __DIR__ . '/Resources/translations'
        ];
        $locator = new FileLocator($configDirectories);

        $translationsFile = $locator->locate(
            sprintf('%s.%s.yml', $domain, $locale), null, true
        );

        return Yaml::parse(file_get_contents($translationsFile));
    }

    public function replaceSymfonyFormattingTagsForVueI18n($value)
    {
        return preg_replace('#%([^%]+)%#', '{$1}', $value);
    }
}