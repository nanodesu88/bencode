<?php

namespace nanodesu88\bencode;

class BencodeInteger extends BencodeElement
{
    /**
     * @var int
     */
    protected $value;

    /**
     * @param int $value
     */
    public function __construct($value = null)
    {
        if ($value) {
            $this->setValue($value);
        }
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = (int)$value;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function encode()
    {
        return 'i' . $this->value . 'e';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * @inheritDoc
     */
    public function compare(BencodeElement $element)
    {
        return $this->encode() == $element->encode();
    }
}