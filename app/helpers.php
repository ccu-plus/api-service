<?php

if (! function_exists('to_ascii')) {
    function to_ascii(string $value): string
    {
        return transliterator_transliterate(
            'Any-Latin; Latin-ASCII',
            $value,
        );
    }
}
