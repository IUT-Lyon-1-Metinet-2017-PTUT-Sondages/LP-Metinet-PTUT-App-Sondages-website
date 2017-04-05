<?php
/**
 * Created by PhpStorm.
 * User: kocal
 * Date: 04/04/17
 * Time: 22:16
 */

namespace AppBundle;


class Helper
{
    public function unflatten($array, $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (!empty($prefix)) {
                $key = preg_replace('#^' . preg_quote($prefix) . '#', '', $key);
            }

            if (strpos($key, '.') !== false) {
                parse_str('result[' . str_replace('.', '][', $key) . "]=" . $value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function replaceSymfonyFormattingTagsForVueI18n($value)
    {
        return preg_replace('#%([^%]+)%#', '{$1}', $value);
    }
}