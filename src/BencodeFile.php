<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 29.12.2016
 * Time: 19:03
 */

namespace nanodesu88\bencode;

class BencodeFile extends BencodeList
{
    public function getLength()
    {
        return (string)$this['length'];
    }

    public function getPath()
    {
        $result = [];

        foreach ($this['path'] as $el) {
            $result[] = $el->getValue();
        }

        return join(DIRECTORY_SEPARATOR, $result);
    }
}