<?php

namespace Marky\BlockFormatters;

use Marky\AbstractBlockFormatter;

class ParagraphFormatter extends AbstractBlockFormatter
{

    public function format($text)
    {
        $markdown = $this->getFormatter();
        $text     = $markdown->hashHTML($text);

        //Remove leading and trailing newlines
        $text  = preg_replace(['/^\n+/', '/\n+$/'], '', $text);
        $lines = array_map(
            function ($line) use ($markdown) {
                if ($markdown->hasHTML($line)) {
                    return $markdown->getHTML($line);
                } else {
                    $line = $markdown->formatLine($line);
                    $line = ltrim($line);

                    return "<p>{$line}</p>";
                }
            },
            preg_split('/\n{2,}/', $text)
        );

        return implode("\n\n", $lines);
    }
}
