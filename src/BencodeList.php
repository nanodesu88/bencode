<?php

namespace nanodesu88\bencode;

class BencodeList extends BencodeCollection
{
    /**
     * @var BencodeElement[]
     */
    protected $value = [];

    public function __construct(iterable $source = [])
    {
        foreach ($source as $item) {
            $this->smartAdd(BencodeElement::morph($item));
        }
    }

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
     * @return BencodeElement[]
     */
    public function values()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->value);
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
    public function exists($offset)
    {
        return array_key_exists($offset, $this->value);
    }

    public function unset($offset)
    {
        unset($this[$offset]);
    }

    /**
     * @param mixed $value
     */
    public function push($value)
    {
        $value = BencodeElement::morph($value);

        $this->value[] = $value;

        $value->parent = $this;
    }

    public function clear()
    {
        $this->value = [];
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

    /**
     * @inheritDoc
     */
    public function unMorph()
    {
        $result = [];

        foreach ($this->value as $item) {
            $result[] = $item->unMorph();
        }

        return $result;
    }
}














