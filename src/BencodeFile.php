<?php

namespace nanodesu88\bencode;

class BencodeFile extends BencodeList
{
    public function getLength() {
        return (string)$this->get('length');
    }

    public function getPath() {
        $result = [];

        foreach ($this->get('path') as $el) {
            $result[] = $el->unMorph();
        }

        return join(DIRECTORY_SEPARATOR, $result);
    }
}