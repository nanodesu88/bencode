<?php

namespace nanodesu88\bencode;

class Bencode extends BencodeDictionary
{
    /**
     * @var BencodeCollection
     */
    // public $element;

    /**
     * Bencode constructor.
     */
    /*private function __construct()
    {
        // $this->element;
    }*/

    public function keys()
    {
        $r = [];
        foreach ($this->value as $item) {
            /**
             * @var BencodeElement $key
             * @var BencodeElement $value
             */
            list($key, $value) = [$item['key'], $item['value']];
            $r[] = $key->getValue();
        }

        return $r;
    }

    /**
     * @param string $data
     * @return static
     * @throws BencodeException
     */
    public static function decode($data)
    {
        $handle = fopen('php://memory', 'a');

        fputs($handle, $data);
        fseek($handle, 0);

        if ($data[0] != 'd') {
            throw new BencodeException();
        }
        /** @var BencodeCollection $current */
        $root = $current = null;

        for ($pos = 0, $len = strlen($data); $pos < $len; $pos = ftell($handle)) {
            $c = fgetc($handle);

            switch ($c) {
                case 'd':
                    if ($root === null) {
                        $t = new static();
                    } else {
                        $t = new BencodeDictionary();
                    }

                    if ($root === null) {
                        $root = $t;
                    } else {
                        $current->smartAdd($t);
                    }

                    $current = $t;
                    break;
                case 'l':
                    $t = new BencodeList();

                    if ($root === null) {
                        $root = $t;
                    } else {
                        $current->smartAdd($t);
                    }

                    $current = $t;
                    break;
                case 'i':
                    $value = '';

                    while (($nextC = fgetc($handle)) != 'e') {
                        $value .= $nextC;
                    }

                    $e = new BencodeInteger($value);

                    $current->smartAdd($e);
                    break;
                case 'e':
                    $current = $current->parent;
                    break;
                default:
                    $stringLength = $c;

                    while (is_numeric($nextC = fgetc($handle))) {
                        $stringLength .= $nextC;
                    }

                    $e = new BencodeString((int)$stringLength > 0 ? fread($handle, (int)$stringLength) : '');

                    if ($current instanceof BencodeElement) {
                        $current->smartAdd($e);
                    }
                    break;
            }
        }

        return $root;
    }
}