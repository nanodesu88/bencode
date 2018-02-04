<?php

namespace nanodesu88\bencode;

class BencodeDictionaryPair
{
    /**
     * @var BencodeElement
     */
    public $key;

    /**
     * @var BencodeElement
     */
    public $value;

    public function __construct(BencodeElement $key, BencodeElement $value)
    {
        $this->key   = $key;
        $this->value = $value;
    }
}