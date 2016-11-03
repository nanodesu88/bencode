<?php

namespace nanodesu88\bencode;

abstract class BencodeElement
{
    /**
     * @var BencodeElement
     */
    public $parent;
    /**
     * @var bool
     */
    public $sortData = false;

    /**
     * @return string
     */
    abstract public function encode();

    /**
     * @param BencodeElement $element
     * @return bool
     */
    abstract public function compare(BencodeElement $element);

    /**
     * @param $value
     * @return BencodeElement
     */
    public static function parse($value)
    {
        if ($value instanceof BencodeElement) {
            return $value;
        }

        if (is_integer($value)) {
            return new BencodeInteger($value);
        } else {
            return new BencodeString($value);
        }
    }
}