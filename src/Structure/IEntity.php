<?php

namespace nanodesu88\bencode\Structure;

use nanodesu88\bencode\BencodeDictionary;

interface IEntity
{
    public function load(BencodeDictionary $bencodeElement);
    public function encode();
}