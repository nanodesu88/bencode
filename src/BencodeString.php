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
    public function __construct($value = '') {
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value) {
        $this->value = $value;
    }

    public function set(string $value) {
        return $this->setValue($value);
    }

    /**
     * @inheritdoc
     */
    public function encode() {
        parent::encode();

        return strlen($this->value) . ':' . $this->value;
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string)$this->value;
    }

    /**
     * @inheritDoc
     */
    public function compare(BencodeElement $element) {
        return $this->encode() === $element->encode();
    }

    /**
     * @return string
     */
    public function unMorph() {
        return $this->getValue();
    }

    /**
     * @param $string
     * @return $this
     */
    public function concat($string) {
        $this->value .= $string;

        return $this;
    }
}