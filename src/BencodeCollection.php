



<?php

namespace nanodesu88\bencode;

abstract class BencodeCollection extends BencodeElement implements \Countable, \IteratorAggregate
{
    /**
     * @param BencodeElement $e
     * @return mixed
     */
    public abstract function smartAdd(BencodeElement $e);
}