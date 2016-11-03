<?php

namespace nanodesu88\bencode;

class BencodeDictionary extends BencodeCollection
{
    /**
     * @var array
     */
    protected $value = [];

    /**
     * @inheritDoc
     */
    public function encode()
    {
        $data = 'd';
        foreach ($this->value as $item) {
            /**
             * @var BencodeElement $key
             * @var BencodeElement $value
             */
            list($key, $value) = [$item['key'], $item['value']];
            $data .= $key->encode() . $value->encode();
        }
        return $data . 'e';
    }

    /**
     * @var BencodeElement
     */
    protected $_smart;

    public function smartAdd(BencodeElement $e)
    {
        if ($this->_smart === null) {
            $this->_smart = $e;
        } else {
            $this[$this->_smart] = $e;
            $this->_smart = null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        $offset = static::parse($offset);

        foreach ($this->value as $index => $pair) {
            /**
             * @var BencodeElement $key
             * @var BencodeElement $value
             */
            list($key, $value) = [$pair['key'], $pair['value']];

            if ($key->compare($offset)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        $offset = static::parse($offset);

        foreach ($this->value as $item) {
            /**
             * @var BencodeElement $key
             * @var BencodeElement $value
             */
            list($key, $value) = [$item['key'], $item['value']];

            if ($key->compare($offset)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param BencodeElement $offset
     * @param BencodeElement $value
     * @throws BencodeException
     */
    public function offsetSet($offset, $value)
    {
        if (!$offset instanceof BencodeElement) {
            throw new BencodeException();
        }

        if (isset($this[$offset])) {

        } else {
            $this->value[] = ['key' => $offset, 'value' => $value];
            $value->parent = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        // TODO: Implement count() method.
    }

    /**
     * @inheritDoc
     */
    public function compare(BencodeElement $element)
    {
        return false;
    }


}