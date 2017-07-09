<?php

namespace nanodesu88\bencode\Structure;

use \ArrayObject;
use Illuminate\Support\Arr;
use nanodesu88\bencode\Bencode;
use nanodesu88\bencode\BencodeElement;
use nanodesu88\bencode\BencodeList;

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
     * @var string
     */
    private $announce = '';

    /**
     * @var ArrayObject
     */
    private $announces;

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

    public function getAnnounce(): string
    {
        return $this->announce;
    }

    public function setAnnounce(string $value)
    {
        $this->announce = $value;
    }

    /**
     * @return ArrayObject
     */
    public function getAnnounces()
    {
        return $this->announces;
    }

    public function __construct()
    {
        $this->announces = new ArrayObject();
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

        if ($result->countFiles > 1) {
            foreach ($files as $file) {
                $path = [];

                foreach ($file->getValue('path') as $item) {
                    $path[] = $item->getValue();
                }

                $result->files[join(DIRECTORY_SEPARATOR, $path)] = $file->getValue('length')->getValue();
            }
        }

        $result->announce = $result->getValue('announce')->getValue();
        
        if ($announceList = $result->getValue('announce-list')) {
            $result->announces->exchangeArray(array_map(function (BencodeElement $element) {
                return $element->values()[0]->getValue();
            }, $announceList->values()));
        }

        return $result;
    }

    public function prepare()
    {
        parent::prepare();

        $this->getValue('announce')->setValue($this->announce);

        $announces = $this->getValue('announce-list');
        
        if (!$announces) {
            $announces = new BencodeList();
            $this->setValue('announce-list', $announces);
        }
        
        $announces->clear();

        foreach ($this->announces as $announce) {
            $announces->push($announce);
        }
    }
}
