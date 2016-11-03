<?php

namespace nanodesu88\bencode;

class BencodeString extends BencodeElement
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $value
     */
    public function __construct($value = '')
    {
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = (string) $value;
    }

    /**
     * @inheritdoc
     */
    public function encode()
    {
        return strlen($this->value) . ':' . $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * @inheritDoc
     */
    public function compare(BencodeElement $element)
    {
        return $this->encode() == $element->encode();
    }
}