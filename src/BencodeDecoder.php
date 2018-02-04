<?php

namespace nanodesu88\bencode;

use nanodesu88\bencode\Structure\IEntity;

class BencodeDecoder
{
    /**
     * @var resource
     */
    protected $handle;

    public function __construct()
    {

    }

    /**
     * @param string       $data
     * @param IEntity|null $structure
     * @return BencodeDictionary|BencodeInteger|BencodeList|BencodeString|IEntity|null
     */
    public function decode(string $data, IEntity $structure = null)
    {
        $this->handle = fopen('php://memory', 'a');

        fputs($this->handle, $data);
        fseek($this->handle, 0);

        $root = null;
        /** @var BencodeCollection|null $current */
        $current = null;

        for ($pos = 0, $len = strlen($data); $pos < $len; $pos = ftell($this->handle)) {
            $c = fgetc($this->handle);

            switch ($c) {
                case 'd':
                    $temp = new BencodeDictionary();

                    if ($root === null) {
                        $root = $temp;
                    } else {
                        $current->smartAdd($temp);
                    }

                    $current = $temp;
                    break;
                case 'l':
                    $temp = new BencodeList();

                    if ($root === null) {
                        $root = $temp;
                    } else {
                        $current->smartAdd($temp);
                    }

                    $current = $temp;
                    break;
                case 'i':
                    $value = '';

                    while (($nextC = fgetc($this->handle)) != 'e') {
                        $value .= $nextC;
                    }

                    $e = new BencodeInteger($value);

                    if ($root === null) {
                        $root = $e;
                    } else {
                        $current->smartAdd($e);
                    }
                    break;
                case 'e':
                    $current = $current->parent;
                    break;
                default:
                    $stringLength = $c;

                    while (is_numeric($nextC = fgetc($this->handle))) {
                        $stringLength .= $nextC;
                    }

                    $e = new BencodeString((int)$stringLength > 0 ? fread($this->handle, (int)$stringLength) : '');

                    if ($current instanceof BencodeCollection) {
                        $current->smartAdd($e);
                    } else if ($root === null) {
                        $root = $e;
                    }
                    break;
            }
        }

        fclose($this->handle);

        if ($structure) {
            $structure->load($root);

            return $structure;
        }

        return $root;
    }
}