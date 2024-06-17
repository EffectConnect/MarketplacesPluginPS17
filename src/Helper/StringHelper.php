<?php

namespace EffectConnect\Marketplaces\Helper;

/**
 * Class StringHelper
 * @package EffectConnect\Marketplaces\Helper
 */
class StringHelper
{
    /**
     * Generate a slug from a string.
     *
     * @param string $text
     * @param string $divider
     */
    public static function slugify(string $text, string $divider = '_')
    {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}