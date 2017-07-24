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

    /**
     * @param BencodeElement $element
     * @return bool
     */
    abstract public function compare(BencodeElement $element);

    public function prepare()
    {

    }

    /**
     * @param $value
     * @return BencodeElement
     */
    public static function parse($value)
    {
        if ($value instanceof BencodeElement) {
            return $value;
        }

        if (is_array($value)) {
            if (Arr::isAssoc($value)) {
                $result = new BencodeDictionary();

                foreach ($value as $key => $val) {
                    $result->smartAdd(BencodeElement::parse($key));
                    $result->smartAdd(BencodeElement::parse($val));
                }

                return $result;
            } else {

            }
        } else if (is_integer($value)) {
            return new BencodeInteger($value);
        } else {
            return new BencodeString($value);
        }
    }
}