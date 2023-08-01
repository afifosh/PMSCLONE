<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class InputSanitizer
{
    public function sanitize(string $text): string
    {
        $config = (new HtmlSanitizerConfig())
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowSafeElements()
            ->allowAttribute('style', '*')
            ->allowAttribute('class', '*')
            ->allowAttribute('userid', '*');

        $sanitizer = new HtmlSanitizer($config);

        return $sanitizer->sanitize($text);
    }
}
