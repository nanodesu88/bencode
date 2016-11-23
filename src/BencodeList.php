<?php

namespace nanodesu88\bencode;

class BencodeList extends BencodeCollection
{
    /**
     * @var BencodeElement[]
     */
    protected $value = [];

    /**
     * @inheritDoc
     */
    public function encode()
    {
        $data = 'l';
        foreach ($this->value as $item) {
            $data .= $item->encode();
        }
        return $data . 'e';
    }

    /**
     * @inheritDoc
     */
    public function smartAdd(BencodeElement $e)
    {
        $this[] = $e;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->value);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->value);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->value[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->value[] = $value;
        } else {
            $this->value[$offset] = $value;
        }

        $value->parent = $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->value[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->value);
    }

    /**
     * @inheritDoc
     */
    public function compare(BencodeElement $element)
    {
        return false;
    }


}