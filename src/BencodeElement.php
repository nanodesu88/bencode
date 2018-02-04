<?php

namespace nanodesu88\bencode;

use Illuminate\Support\Arr;

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
    public function encode()
    {
        $this->prepare();

        return '';
    }

    public function prepare()
    {

    }

    /**
     * @param $value
     * @return BencodeElement
     */
    public static function morph($value)
    {
        if ($value instanceof BencodeElement) {
            return $value;
        }

        if (is_array($value)) {
            if (Arr::isAssoc($value)) {
                return new BencodeDictionary($value);
            } else {
                return new BencodeList($value);
            }
        } else if (is_integer($value)) {
            return new BencodeInteger($value);
        } else {
            return new BencodeString($value);
        }
    }

    /**
     * @return mixed
     */
    public abstract function unMorph();

    /**
     * @param BencodeElement $element
     * @return bool
     */
    abstract public function compare(BencodeElement $element);

}