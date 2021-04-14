<?php

namespace Sparors\Ussd;
use DOMAttr;
use DOMDocument;
class Menu
{
    const NUMBERING_ALPHABETIC_LOWER = 'alphabetic_lower';
    const NUMBERING_ALPHABETIC_UPPER = 'alphabetic_lower';
    const NUMBERING_EMPTY = 'empty';
    const NUMBERING_NUMERIC = 'numeric';
    const ITEMS_SEPARATOR_NO_LINE_BREAK = "";
    const ITEMS_SEPARATOR_LINE_BREAK = "\n";
    const ITEMS_SEPARATOR_DOUBLE_LINE_BREAK = "\n\n";
    const NUMBERING_SEPARATOR_NO_SPACE = "";
    const NUMBERING_SEPARATOR_SPACE = " ";
    const NUMBERING_SEPARATOR_DOUBLE_SPACE = "  ";
    const NUMBERING_SEPARATOR_DOT = ".";
    const NUMBERING_SEPARATOR_DOT_PLUS_SPACE = ". ";
    const NUMBERING_SEPARATOR_DOT_PLUS_DOUBLE_SPACE = ".  ";
    const NUMBERING_SEPARATOR_BRACKET = ")";
    const NUMBERING_SEPARATOR_BRACKET_PLUS_SPACE = ") ";
    const NUMBERING_SEPARATOR_BRACKET_PLUS_DOUBLE_SPACE = ")  ";
    /** @var string */
    protected $menu;
    /** @var string */
    protected $xmlmenu;
    protected function replaceSpecialChar($str) {
        $ch0 = array( 
                "œ"=>"oe",
                "Œ"=>"OE",
                "æ"=>"ae",
                "Æ"=>"AE",
                "À" => "A",
                "Á" => "A",
                "Â" => "A",
                "à" => "A",
                "Ä" => "A",
                "Å" => "A",
                "à" => "a",
                "á" => "a",
                "â" => "a",
                "à" => "a",
                "ä" => "a",
                "å" => "a",
                "Ç" => "C",
                "ç" => "c",
                "Ð" => "D",
                "È" => "E",
                "É" => "E",
                "Ê" => "E",
                "Ë" => "E",
                "è" => "e",
                "é" => "e",
                "ê" => "e",
                "ë" => "e",
                "’" => " ",
                "'" => " ",
                "\"" => "*",
                "..." => ". . .",
                "Ì" => "I",
                "Í" => "I",
                "Î" => "I",
                "Ñ" => "N",
                "Ò" => "O",
                "Ó" => "O",
                "Ô" => "O",
                "Õ" => "O",
                "Ö" => "O",
                "Ø" => "O",
                "ò" => "o",
                "ó" => "o",
                "ô" => "o",
                "õ" => "o",
                "ö" => "o",
                "ø" => "o",
                "ð" => "o",
                "Ù" => "U",
                "Ú" => "U",
                "Û" => "U",
                "Ü" => "U",
                "ù" => "u",
                "ú" => "u",
                "û" => "u",
                "ü" => "u",
                "Ý" => "Y",
                "?" => "Y",
                "ý" => "y",
                "ÿ" => "y",
                );
            $str = strtr($str,$ch0);
            return $str;
        }
    public function __construct($menu = '')
    {
        $this->menu = $menu;
        $this->xmlmenu = $menu;
    }
    public function xmlmenu(array $params){

        $dom = new DOMDocument('1.0','utf-8');
        $root = $dom->createElement('response');
        $dom->appendChild($root);
        $screen_type = $dom->createElement('screen_type',$this->replaceSpecialChar($params['screen_type']));
        $root->appendChild($screen_type);
        $text = $dom->createElement('text',$this->replaceSpecialChar($params['text']));
        $root->appendChild($text);
        $back_link = $dom->createElement('back_link',$this->replaceSpecialChar($params['back_link']));
        $root->appendChild($back_link);
        $home_link = $dom->createElement('home_link',$this->replaceSpecialChar($params['home_link']));
        $root->appendChild($home_link);
        $session_op = $dom->createElement('session_op',$this->replaceSpecialChar($params['session_op']));
        $root->appendChild($session_op);
        $screen_id = $dom->createElement('screen_id',$this->replaceSpecialChar($params['screen_id']));
        $root->appendChild($screen_id);
        $options = $dom->createElement('options');
        if($params['list']){
            for ($i=0; $i < count($params['list']) ; $i++) { 
                $numero= $i+1;
                $option = $dom->createElement('option',$this->replaceSpecialChar($params['list'][$i]));
                $options->appendChild($option);
                $attr = new DOMAttr('choice',$numero);
                $option->setAttributeNode($attr);
            }
        }
        $root->appendChild($options);
        //create options childs according to params list elements
        $this->menu = $dom->saveXML();
        return $this;
    }
    
    protected function numberingFor(int $index, string $numbering): string
    {
        if ($numbering === self::NUMBERING_ALPHABETIC_LOWER) {
            return range('a','z')[$index];
        }
        if ($numbering === self::NUMBERING_ALPHABETIC_UPPER) {
            return range('A','Z')[$index];
        }
        if ($numbering === self::NUMBERING_NUMERIC) {
            return (string) $index + 1;
        }
        return '';
    }

    protected function isLastPage(
        int $page,
        int $numberPerPage,
        array $items
    ): bool {
        return $page * $numberPerPage >= count($items);
    }
    protected function pageStartIndex(int $page, int $numberPerPage): int
    {
        return $page * $numberPerPage - $numberPerPage;
    }

    protected function pageLimit(int $page, int $numberPerPage, array $items): int
    {
        return (
            $this->isLastPage($page, $numberPerPage, $items)
            ? count($items) - $this->pageStartIndex($page, $numberPerPage)
            : $numberPerPage
        );
    }

    private function listParser(
        array $items,
        int $page,
        int $numberPerPage,
        string $numberingSeparator,
        string $itemsSeparator,
        string $numbering
        ): void {
        $startIndex = $this->pageStartIndex($page, $numberPerPage);
        $limit = $this->pageLimit($page, $numberPerPage, $items);
        for ($i = 0; $i < $limit; $i++) {
            $this->menu .= "{$this->numberingFor($i + $startIndex, $numbering)}{$numberingSeparator}{$items[$i + $startIndex]}";
            if ($i !== $limit - 1) {
                $this->menu .= $itemsSeparator;
            }
        }
    }

    public function lineBreak(int $number = 1): self
    {
        $this->menu .= str_repeat("\n", $number);

        return $this;
    }

    public function line(string $text): self
    {
        $this->menu .= "$text\n";

        return $this;
    }

    public function text(string $text): self
    {
        $this->menu .= $text;

        return $this;
    }

    public function listing(
        array $items,
        string $numberingSeparator = self::NUMBERING_SEPARATOR_DOT,
        string $itemsSeparator = self::ITEMS_SEPARATOR_LINE_BREAK,
        string $numbering = self::NUMBERING_NUMERIC
    ): self {
        $this->listParser(
            $items,
            1,
            count($items),
            $numberingSeparator,
            $itemsSeparator,
            $numbering
        );

        return $this;
    }

    public function paginateListing(
        array $items,
        int $page = 1,
        int $numberPerPage = 5,
        string $numberingSeparator = self::NUMBERING_SEPARATOR_DOT,
        string $itemsSeparator = self::ITEMS_SEPARATOR_LINE_BREAK,
        string $numbering = self::NUMBERING_NUMERIC
    ): self {
        $this->listParser(
            $items,
            $page,
            $numberPerPage,
            $numberingSeparator,
            $itemsSeparator,
            $numbering
        );
        return $this;
    }

    public function toString(): string
    {
        return $this->menu;
    }

    public function __toString()
    {
        return $this->menu;
    }
   
}
