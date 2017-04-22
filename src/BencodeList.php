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
        parent::encode();

        $data = 'l';

        foreach ($this->value as $item) {
            $data .= $item->encode();
        }

        return $data . 'e';
    }

    /**
     * @return BencodeElement[]
     */
    public function values()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function smartAdd(BencodeElement $e)
    {
        $this->push($e);
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

    public function push(BencodeElement $value)
    {
        $this->value[] = $value;

        $value->parent = $this;
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














