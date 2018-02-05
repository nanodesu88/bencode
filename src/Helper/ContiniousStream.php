<?php

namespace nanodesu88\bencode\Helper;

class ContiniousStream
{
    protected $iterator;

    /**
     * @var resource
     */
    protected $current;

    public function __construct(\Iterator $streamIterator) {
        $this->iterator = $streamIterator;
    }

    /**
     * @param int $length
     * @return string
     */
    public function read(int $length) {
        if (!$this->iterator->valid()) {
            return false;
        }

        if (!$this->current) {
            $this->current = $this->iterator->current();
        }

        $content = fread($this->current, $length);

        if (strlen($content) < $length) {
            $s = $length - strlen($content);

            fclose($this->current);

            $this->iterator->next();

            if ($this->iterator->valid()) {
                $this->current = $this->iterator->current();
                $content       .= fread($this->current, $s);
            }
        }

        return $content;
    }
}