<?php

namespace ATCM\Core\Helpers;

/**
 * A helper class to manipulate strings
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class StringHelper
{    
    /**
     * Converts a string to camelCase. By default, assume that words are separated by white spaces,
     * but other separator can be passed, like underscores.
     *
     * @param  mixed $text The original text to be converted
     * @param  mixed $separator Character that separates words in text. The default value is white space.
     * @return string A string in camelCase, without white spaces, starting each word with uppercase letter,
     * expect the first word. Example: "One Two Three" will be converted to "oneTwoThree"
     */
    public static function toCamelCase(string $text, string $separator = " "): string
    {
        $camelCaseText = "";
        if(strpos($text, $separator) === false) {
            $camelCaseText = $text;
        } else {            
            $camelCaseText = self::toPascalCase($text, $separator);
        }
        return lcfirst($camelCaseText);
    }

    /**
     * Converts a string to PascalCase. By default, assume that words are separated by white spaces,
     * but other separator can be passed, like underscores.
     *
     * @param  mixed $text The original text to be converted
     * @param  mixed $separator Character that separates words in text. The default value is white space.
     * @return string A string in PascalCase, without white spaces, starting each word with uppercase letter. 
     * Example: "One Two Three" will be converted to "OneTwoThree"
     */
    public static function toPascalCase(string $text, string $separator = " "): string
    {
        $words = explode($separator, $text);
        $parsedText = "";
        foreach($words as $word) {
            $wordLowercase = strtolower($word);
            $parsedText .= ucfirst($wordLowercase);
        }
        return $parsedText;
    }

    /**
     * Converts a string to snake_case. By default, assume that words are separated by white spaces,
     * but other separator can be passed, like underscores.
     *
     * @param  mixed $text The original text to be converted
     * @param  mixed $separator Character that separates words in text. The default value is white space.
     * @return string A string in snake_case, with underscores separating each word, all sentence in lowercase.
     * Example: "One Two Three" will be converted to "on_two_three"
     */
    public static function toSnakeCase(string $text): string
    {
        $parsedText = "";
        $textLenght = strlen($text);
        $separators = [" ", "-", "_"];
        for ($i = 0; $i < $textLenght; $i++) {
            $character = substr($text, $i, 1);            
            if(in_array($character, $separators)) {
                $parsedText .= "_";
                continue;
            }
            if($i >= 1) {
                $previousCharacter = substr($text, $i - 1, 1);
                if((ctype_upper($character) && ctype_lower($previousCharacter)) && $i > 0) {
                    $parsedText .= "_";
                }
            }
            $parsedText .= strtolower($character);
        }
        return $parsedText;
    }
}