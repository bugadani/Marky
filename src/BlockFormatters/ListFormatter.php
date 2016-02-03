<?php

namespace Marky\BlockFormatters;

use Marky\AbstractBlockFormatter;

class ListFormatter extends AbstractBlockFormatter
{

    public function format($text)
    {
        $listsPattern = '/^(([ ]{0,3}((?:[*+-]|\d+[.]))[ ]+)(?s:.+?)(\z|\n{2,}(?=\S)(?![ ]*(?:[*+-]|\d+[.])[ ]+)))/m';

        $markdown = $this->getFormatter();

        return preg_replace_callback(
            $listsPattern,
            function ($matches) use ($markdown) {
                list(, $list, , $listCharacter) = $matches;

                //Normalize newline count
                $list = preg_replace('/\n{2,}/', "\n\n\n", $list);
                $list = preg_replace('/\n{2,}$/', "\n", $list);

                $list = preg_replace_callback(
                    '/(\n)?(^[ ]*)([*+-]|\d+[.])[ ]+((?s:.+?)(?:\z|\n{1,2}))(?=\n*(?:\z|\2([*+-]|\d+[.])[ ]+))/m',
                    function ($matches) use ($markdown) {
                        list(, $leadingLine, , , $item) = $matches;

                        $outdentedItem = $markdown->outdent($item);
                        if ($leadingLine || (strpos($item, "\n\n") !== false)) {
                            $formatted = $markdown->formatBlock($outdentedItem);
                        } else {
                            $item      = $this->format($outdentedItem);
                            $formatted = $markdown->formatLine(rtrim($item));
                        }

                        $item = $markdown->hashHTML($formatted);

                        return "<li>{$item}</li>\n";
                    },
                    $list
                );

                if (in_array($listCharacter, ['*', '+', '-'])) {
                    $tag = 'ul';
                } else {
                    $tag = 'ol';
                }

                return "<{$tag}>\n{$list}\n</{$tag}>\n";
            },
            $text
        );
    }
}
