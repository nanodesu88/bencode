<?php

namespace nanodesu88\bencode\Structure;

use ArrayObject;
use nanodesu88\bencode\BencodeDictionary;
use nanodesu88\bencode\BencodeElement;
use nanodesu88\bencode\BencodeException;
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

    /**
     * @var BencodeDictionary|null
     */
    private $original;

    public function getSha1() {
        return $this->sha1;
    }

    public function isMulti() {
        return $this->isMulti;
    }

    public function getFiles() {
        return $this->files;
    }

    public function getCountFiles() {
        return $this->countFiles;
    }

    public function getLength() {
        return $this->length;
    }

    public function getAnnounce() {
        return $this->announce;
    }

    public function setAnnounce($value) {
        $this->announce = $value;
    }

    /**
     * @return ArrayObject
     */
    public function getAnnounces() {
        return $this->announces;
    }

    public function __construct() {
        $this->announces = new ArrayObject();
    }

    /**
     * @return string
     * @throws BencodeException
     */
    public function encode() {
        if (!$this->original) {
            throw new BencodeException('no bencode provided');
        }

        $this->original->get('announce')->set($this->announce);
        $this->original->set('announce-list', $announceListBencode = new BencodeList());

        foreach ($this->announces as $announce) {
            $announceListBencode->push($announce);
        }

        return $this->original->encode();
    }

    public function load(BencodeDictionary $bencodeElement) {
        $this->original = $bencodeElement;

        // info
        $this->sha1    = sha1($bencodeElement->get('info')->encode());
        $this->isMulti = $bencodeElement->get('info')->get('files') !== null;

        // torrent length
        if (!$this->isMulti()) {
            $this->length = $bencodeElement->get('info')->get('length')->unMorph();
        } else {
            $length = 0;

            foreach ($bencodeElement->get('info')->get('files') as $key => $file) {
                $length += $file->get('length')->unMorph();
            }

            $this->length = $length;
        }

        // files
        if ($files = $bencodeElement->get('info')->get('files')) {
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

        // announces
        $this->announce = $bencodeElement->getValue('announce')->getValue();

        if ($announceList = $bencodeElement->getValue('announce-list')) {
            $this->announces->exchangeArray(array_map(function (BencodeElement $element) {
                // На случай двухуровнего списка
                if ($element instanceof BencodeList) {
                    return $element->values()[0]->unMorph();
                } else {
                    return $element->unMorph();
                }
            }, $announceList->values()));
        }
    }
}
