<?php

use nanodesu88\bencode\BencodeDecoder;
use nanodesu88\bencode\BencodeDictionary;
use nanodesu88\bencode\BencodeElement;
use nanodesu88\bencode\BencodeInteger;
use nanodesu88\bencode\BencodeList;
use nanodesu88\bencode\BencodeString;
use nanodesu88\bencode\Structure\AnnounceResponse;
use nanodesu88\bencode\Structure\Support\Peer;
use nanodesu88\bencode\Structure\Torrent;
use PHPUnit\Framework\TestCase;

final class ParseTest extends TestCase
{
    protected function getContent() {
        return file_get_contents(__DIR__ . '/../resources/torrent/1.torrent');
    }

    public function testMorph() {
        $string     = BencodeElement::morph('string');
        $integer    = BencodeElement::morph(1);
        $array      = BencodeElement::morph([1, 2]);
        $dictionary = BencodeElement::morph(['a' => 1, 'b' => 2]);

        $this->assertInstanceOf(BencodeString::class, $string);
        $this->assertInstanceOf(BencodeInteger::class, $integer);
        $this->assertInstanceOf(BencodeList::class, $array);
        $this->assertInstanceOf(BencodeDictionary::class, $dictionary);
    }

    public function testUnMorph() {
        $decoder = new BencodeDecoder();

        $integer    = $decoder->decode('i1e');
        $string     = $decoder->decode('6:string');
        $array      = $decoder->decode('l6:stringe');
        $dictionary = $decoder->decode('d6:stringi1ee');

        $this->assertEquals(1, $integer->unMorph());
        $this->assertEquals('string', $string->unMorph());
        $this->assertEquals(['string'], $array->unMorph());
        $this->assertEquals(['string' => 1], $dictionary->unMorph());
    }


    public function testEncode() {
        $decoder = new BencodeDecoder();

        $integer    = $decoder->decode('i1e');
        $string     = $decoder->decode('6:string');
        $array      = $decoder->decode('l6:stringe');
        $dictionary = $decoder->decode('d6:stringi1ee');

        $this->assertEquals('i1e', $integer->encode());
        $this->assertEquals('6:string', $string->encode());
        $this->assertEquals('l6:stringe', $array->encode());
        $this->assertEquals('d6:stringi1ee', $dictionary->encode());
    }

    public function testParseTorrent() {
        $decoder = new BencodeDecoder();

        /** @var Torrent $obj */
        $obj = $decoder->decode($this->getContent(), new Torrent());

        $this->assertInstanceOf(Torrent::class, $obj);
        $this->assertTrue(is_array($obj->getFiles()));
        $this->assertTrue(is_string($obj->getAnnounce()));
        $this->assertTrue(is_string($obj->getSha1()) && strlen($obj->getSha1()) == 40);
    }

    public function testAnnounce() {
        $ann = new AnnounceResponse();

        $ann->trackerId       = 'trackerid';
        $ann->minimumInterval = 60;
        $ann->interval        = 180;
        $ann->warningMessage  = 'warning message';
        $ann->failureReason   = 'failure reason';
        $ann->complete        = 1;
        $ann->incomplete      = 1;
        $ann->peers           = [
            new Peer('127.0.0.1', 65565, '12345678901234567890'),
            new Peer('127.0.0.1', 65566, '12345678901234567890'),
        ];

        $encoded = $ann->encode();

        $decoder = new BencodeDecoder();

        /** @var AnnounceResponse $obj */
        $obj = $decoder->decode($encoded, new AnnounceResponse());

        $this->assertEquals($ann->trackerId, $obj->trackerId);
        $this->assertEquals($ann->minimumInterval, $obj->minimumInterval);
        $this->assertEquals($ann->interval, $obj->interval);
        $this->assertEquals($ann->warningMessage, $obj->warningMessage);
        $this->assertEquals($ann->failureReason, $obj->failureReason);
        $this->assertEquals($ann->complete, $obj->complete);
        $this->assertEquals($ann->incomplete, $obj->incomplete);
    }
}