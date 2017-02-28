<?php

namespace nanodesu88\bencode;

use Illuminate\Support\Arr;

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
        parent::encode();
        
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
        $offset = static::parse($offset);
        $value = static::parse($value);

        if (isset($this[$offset])) {
            foreach ($this->value as $key => $item) {
                if ($item['key']->compare($offset)) {
                    $this->value[$key]['value'] = $value;
                    ksort($this->value);
                    break;
                }
            }
        } else {
            $this->value[] = ['key' => $offset, 'value' => $value];
        }

        $value->parent = $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $offset = static::parse($offset);

        foreach ($this->value as $i => $item) {
            /**
             * @var BencodeElement $key
             * @var BencodeElement $value
             */
            list($key, $value) = [$item['key'], $item['value']];

            if ($offset->compare($key)) {
                array_splice($this->value, $i, 1);

                break;
            }
        }
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