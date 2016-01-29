<?php

namespace Marky;

use Marky\BlockFormatters\BlockQuoteFormatter;
use Marky\BlockFormatters\CodeBlockFormatter;
use Marky\BlockFormatters\HeadingFormatter;
use Marky\BlockFormatters\HorizontalRuleFormatter;
use Marky\BlockFormatters\ListFormatter;
use Marky\BlockFormatters\ParagraphFormatter;
use Marky\LineFormatters\StandardFormatters;

class Markdown
{
    /**
     * @var AbstractBlockFormatter[]
     */
    protected $blockFormatters = [];

    /**
     * @var AbstractLineFormatter[]
     */
    protected $lineFormatters = [];
    protected $links          = [];
    protected $htmlBlocks     = [];
    private   $htmlPatterns;

    public function escape($str)
    {
        return addcslashes($str, '`*_{}[]()#+\'-.!');
    }

    public function unescape($str)
    {
        return strtr(
            $str,
            [
                "\\'"  => "'",
                '\*'   => '*',
                '\_'   => '_',
                '\`'   => '`',
                '\{'   => '{',
                '\}'   => '}',
                '\['   => '[',
                '\]'   => ']',
                '\('   => '(',
                '\)'   => ')',
                '\#'   => '#',
                '\+'   => '+',
                '\-'   => '-',
                '\.'   => '.',
                '\!'   => '!',
                '\\\\' => '\\'
            ]
        );
    }

    public function outdent($text)
    {
        return preg_replace('/^([ ]{1,4})/m', '', $text);
    }

    public function addLineFormatter(AbstractLineFormatter $formatter)
    {
        array_unshift($this->lineFormatters, $formatter);
    }

    public function addBlockFormatter(AbstractBlockFormatter $formatter)
    {
        array_unshift($this->blockFormatters, $formatter);
    }

    public function __construct()
    {
        $this->addLineFormatter(new StandardFormatters($this));

        $this->addBlockFormatter(new ParagraphFormatter($this));
        $this->addBlockFormatter(new BlockQuoteFormatter($this));
        $this->addBlockFormatter(new CodeBlockFormatter($this));
        $this->addBlockFormatter(new ListFormatter($this));
        $this->addBlockFormatter(new HorizontalRuleFormatter($this));
        $this->addBlockFormatter(new HeadingFormatter($this));

        $blockTags = [
            'p',
            'div',
            'h[1-6]',
            'blockquote',
            'pre',
            'code',
            'table',
            'dl',
            'ol',
            'ul',
            'script',
            'noscript',
            'form',
            'fieldset',
            'iframe',
            'math',
            'ins',
            'del'
        ];

        $this->htmlPatterns = [
            '#^<((' . implode('|', $blockTags) . ')(?:\b.*?)?)>(.*?)</\2>#sm',
            '#(?:(?<=\n\n)|\A\n?)([ ]{0,3}<(hr)\b([^<>])*?/?>[ \t]*(?=\n{2,}|\Z))#m',
            '#(?:(?<=\n\n)|\A\n?)([ ]{0,3}(?s:<!(--.*?--\s*)+>)[ \t]*(?=\n{2,}|\Z))#m'
        ];
    }

    public function formatLine($line)
    {
        foreach ($this->lineFormatters as $formatter) {
            $line = preg_replace_callback(
                $formatter->getPattern(),
                [$formatter, 'format'],
                $line
            );
        }

        return nl2br($line);
    }

    public function formatBlock($text)
    {
        foreach ($this->blockFormatters as $formatter) {
            $text = $formatter->format($text);
        }

        return $text;
    }

    public function hasHTML($key)
    {
        return isset($this->htmlBlocks[ $key ]);
    }

    public function getHTML($key)
    {
        if (!isset($this->htmlBlocks[ $key ])) {
            return $key;
        }

        list($opening, $content, $closing) = $this->htmlBlocks[ $key ];

        if ($opening === 'hr') {
            return $closing;
        }

        $lines   = preg_split('/\n{2,}/', $content);
        $lines   = array_map([$this, 'getHTML'], $lines);
        $content = implode("\n", $lines);

        return "<{$opening}>{$content}</{$closing}>";
    }

    public function hashHTML($text)
    {
        return preg_replace_callback(
            $this->htmlPatterns,
            function ($matches) {
                $key                      = md5($matches[0]);
                $this->htmlBlocks[ $key ] = [$matches[2], $matches[3], $matches[1]];

                return "\n\n{$key}\n\n";
            },
            $text
        );
    }

    private function prepare($text)
    {
        $arr  = [
            "\r\n" => "\n",
            "\r"   => "\n",
            "\t"   => '    ',
        ];
        $text = strtr($text, $arr);
        $text = preg_replace('/^\s*$/m', '', $text);
        $text = $this->hashHTML($text);

        foreach ($this->lineFormatters as $formatter) {
            $text = $formatter->prepare($text);
        }

        return $text;
    }

    public function format($text)
    {
        $this->links      = [];
        $this->htmlBlocks = [];
        $text             = $this->prepare($text);
        $formatted        = $this->formatBlock($text);

        return $this->unescape($formatted);
    }
}
