<?php

namespace nanodesu88\bencode;

class Bencode
{
    /**
     * @var BencodeCollection
     */
    public $element;

    /**
     * Bencode constructor.
     */
    private function __construct()
    {
        $this->element;
    }

    /**
     * @return bool
     */
    public function isMulti()
    {
        return isset($this->element['info']['files']);
    }

    /**
     * @return int
     */
    public function getSize()
    {
        if (!$this->isMulti()) {
            return $this->element['info']['length']->getValue();
        }

        $length = 0;

        foreach ($this->element['info']['files'] as $key => $file) {
            $length += $file['length']->getValue();
        }

        return $length;
    }

    /**
     * @return int
     */
    public function getCountFiles()
    {
        if (isset($this->element['info']['files'])) {
            return count($this->element['info']['files']);
        }

        return 1;
    }

    /**
     * @return string
     */
    public function getSha1()
    {
        return sha1($this->element['info']->encode());
    }

    /**
     * @param string $data
     * @return static
     * @throws BencodeException
     */
    public static function encode($data)
    {
        $handle = fopen('php://memory', 'a');

        fputs($handle, $data);
        fseek($handle, 0);

        $root = NULL;
        /** @var BencodeCollection $current */
        $current = NULL;

        for ($pos = 0, $len = strlen($data); $pos < $len; $pos = ftell($handle)) {
            $c = fgetc($handle);

            switch ($c) {
                case 'd':
                    $t = new BencodeDictionary();

                    if ($root === NULL) {
                        $root = $t;
                    } else {
                        $current->smartAdd($t);
                    }

                    $current = $t;
                    break;
                case 'l':
                    $t = new BencodeList();

                    if ($root === NULL) {
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

                    $e = new BencodeString((int) $stringLength > 0 ? fread($handle, (int) $stringLength) : '');

                    if (!$current instanceof BencodeElement) {
                        throw new BencodeException();
                    }

                    $current->smartAdd($e);
                    break;
            }
        }

        $obj = new static();
        $obj->element = $root;

        return $obj;
    }
}