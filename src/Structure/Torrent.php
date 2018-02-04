<?php

namespace nanodesu88\bencode\Structure;

use \ArrayObject;
use Illuminate\Support\Arr;
use nanodesu88\bencode\Bencode;
use nanodesu88\bencode\BencodeDictionary;
use nanodesu88\bencode\BencodeElement;
use nanodesu88\bencode\BencodeList;

class Torrent implements IEntity
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

    public function getSha1()
    {
        return $this->sha1;
    }

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

    public function getAnnounce()
    {
        return $this->announce;
    }

    public function setAnnounce($value)
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

    public function load(BencodeDictionary $bencodeElement)
    {
        $this->sha1    = sha1($bencodeElement->getValue('info')->encode());
        $this->isMulti = $bencodeElement->getValue('info')->getValue('files') !== null;

        if (!$this->isMulti()) {
            $this->length = $bencodeElement->getValue('info')->getValue('length')->unMorph();
        } else {
            $length = 0;

            foreach ($bencodeElement->getValue('info')->getValue('files') as $key => $file) {
                $length += $file->getValue('length')->unMorph();
            }

            $this->length = $length;
        }

        if ($files = $bencodeElement->getValue('info')->getValue('files')) {
            $this->countFiles = count($files);
        } else {
            $this->countFiles = 1;
        }

        if ($this->countFiles > 1) {
            foreach ($files as $file) {
                $path = [];

                foreach ($file->getValue('path') as $item) {
                    $path[] = $item->getValue();
                }

                $this->files[join(DIRECTORY_SEPARATOR, $path)] = $file->getValue('length')->getValue();
            }
        }

        $this->announce = $bencodeElement->getValue('announce')->getValue();

        if ($announceList = $bencodeElement->getValue('announce-list')) {
            $this->announces->exchangeArray(array_map(function (BencodeElement $element) {
                // На случай двухуровнего списка
                if ($element instanceof BencodeList) {
                    return $element->values()[0]->getValue();
                } else {
                    return $element->getValue();
                }
            }, $announceList->values()));
        }
    }
}
