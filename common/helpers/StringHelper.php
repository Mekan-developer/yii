<?php

namespace common\helpers;

class StringHelper
{
    public static function generateVerificationCode($length = 4)
    {
        $code = '';
        for($i = 0; $i < $length; $i++) {
            $code .= rand(0, 9);
        }
        return $code;
    }

    public static function generateFileKey()
    {
        return strtolower(static::generateRandomPublicString(8) . '-' .
            static::generateRandomPublicString(4) . '-' .
            static::generateRandomPublicString(4) . '-' .
            static::generateRandomPublicString(4) . '-' .
            static::generateRandomPublicString(12));
    }

    /**
     * Generates a random string of specified length.
     * The string generated matches [A-Z0-9]+
     *
     * @param int $length the length of the key in characters
     * @return string the generated random key
     * @throws \Exception on failure.
     */
    public static function generateRandomPublicString($length = 8)
    {
        $dictionary = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        if (!is_int($length)) {
            throw new \Exception('First parameter ($length) must be an integer');
        }

        if ($length < 1) {
            throw new \Exception('First parameter ($length) must be greater than 0');
        }

        $result = '';
        for($i = 0; $i < $length; $i++) {
            $index = random_int(0, strlen($dictionary) - 1);
            $result .= substr($dictionary, $index, 1);
        }

        return $result;
    }

    static protected function _getSlugTransliterator()
    {
        static $slug = null;
        if ($slug == null) {
            $rules = <<<'RULES'
    :: Any-Latin;
    :: NFD;
    :: [:Nonspacing Mark:] Remove;
    :: NFC;
    :: [^-[:^Punctuation:]] Remove;
    :: Lower();
    [:^L:] { [-] > ;
    [-] } [:^L:] > ;
    [-[:Separator:]]+ > '-';
RULES;
            $slug = \Transliterator::createFromRules($rules);
        }
        return $slug;
    }

    static function getSlug($string)
    {
        $slug = static::_getSlugTransliterator()->transliterate( $string );
        $slug = str_replace('ʺ', 'j', $slug);
        return $slug;
    }
}