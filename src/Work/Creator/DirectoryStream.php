<?php

namespace nanodesu88\bencode\Work\Creator;

use Symfony\Component\Finder\SplFileInfo;

class DirectoryStream implements \Iterator
{
    protected $position = 0;

    /**
     * @var SplFileInfo[]
     */
    protected $values = [];

    public function __construct(array $values = []) {
        $this->values = $values;
    }

    /**
     * @inheritDoc
     */
    public function current() {
        return fopen($this->values[$this->position]->getPathname(), 'r');
    }

    /**
     * @inheritDoc
     */
    public function next() {
        $this->position++;
    }

    /**
     * @inheritDoc
     */
    public function key() {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function valid() {
        return isset($this->values[$this->position]);
    }

    /**
     * @inheritDoc
     */
    public function rewind() {
        $this->position = 0;
    }
}