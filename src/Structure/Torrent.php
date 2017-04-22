<?php

namespace nanodesu88\bencode\Structure;

use Illuminate\Support\Arr;
use nanodesu88\bencode\Bencode;
use nanodesu88\bencode\BencodeElement;

class Torrent extends Bencode
{
    /**
     * @var string
     */
    private $sha1;

    /**
     * @var bool
     */
    private $isMulti;

    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     */
    private $countFiles;

    /**
     * @var string[]
     */
    private $files = [];

    /**
     * @return string
     */
    public function getSha1()
    {
        return $this->sha1;
    }

    /**
     * @return bool
     */
    public function isMulti()
    {
        return $this->isMulti;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getCountFiles()
    {
        return $this->countFiles;
    }

    public function getLength()
    {
        return $this->length;
    }

    public static function decode($data)
    {
        /** @var static $result */
        $result = parent::decode($data);

        $result->sha1 = sha1($result->getValue('info')->encode());
        $result->isMulti = $result->getValue('info')->getValue('files') !== null;

        if (!$result->isMulti()) {
            $result->length = $result->getValue('info')->getValue('length')->getValue();
        } else {
            $length = 0;

            foreach ($result->getValue('info')->getValue('files') as $key => $file) {
                $length += $file->getValue('length')->getValue();
            }

            $result->length = $length;
        }

        if ($files = $result->getValue('info')->getValue('files')) {
            $result->countFiles = count($files);
        } else {
            $result->countFiles = 1;
        }

        if ($result->countFiles) {
            foreach ($files as $file) {
                $path = [];

                foreach ($file->getValue('path') as $item) {
                    $path[] = $item->getValue();
                }

                $result->files[join(DIRECTORY_SEPARATOR, $path)] = $file->getValue('length')->getValue();
            }
        }

        return $result;
    }

}