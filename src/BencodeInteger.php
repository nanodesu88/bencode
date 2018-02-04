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
    public function __construct(int $value)
    {
        $this->setValue($value);
    }

    /**
     * @param $value
     */
    public function setValue(int $value)
    {
        $this->value = $value;
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
     * @inheritDoc
     */
    public function compare(BencodeElement $element)
    {
        return $this->encode() === $element->encode() && get_class($this) && get_class($element);
    }

    /**
     * @return int
     */
    public function unMorph()
    {
        return $this->getValue();
    }

}