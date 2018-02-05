<?php

namespace nanodesu88\bencode\Structure;

use nanodesu88\bencode\BencodeDictionary;
use nanodesu88\bencode\BencodeList;
use nanodesu88\bencode\BencodeString;
use nanodesu88\bencode\Structure\Support\Peer;
use nanodesu88\bencode\Structure\Support\PeerInterface;

/**
 * Ответ трекера на запрос
 */
class AnnounceResponse implements IEntity
{
    /**
     * @var int
     */
    public $interval;

    /**
     * @var int|null
     */
    public $minimumInterval;

    /**
     * @var string
     */
    public $trackerId;

    /**
     * @var Peer[]
     */
    public $peers = [];

    /**
     * @var int
     */
    public $complete = 0;

    /**
     * @var int
     */
    public $incomplete = 0;

    /**
     * @var string|null
     */
    public $failureReason;

    /**
     * @var string|null
     */
    public $warningMessage;

    /**
     * @var bool
     */
    public $compact = false;

    /**
     * @param BencodeDictionary $bencodeElement
     * @throws \nanodesu88\bencode\BencodeException
     */
    public function load(BencodeDictionary $bencodeElement) {
        // $this->compact = (boolean)$bencodeElement->get('compact')->unMorph();

        $this->interval   = $bencodeElement->get('interval')->unMorph();
        $this->incomplete = $bencodeElement->get('incomplete')->unMorph();
        $this->complete   = $bencodeElement->get('complete')->unMorph();

        if ($bencodeElement->exists('tracker id')) {
            $this->trackerId = $bencodeElement->get('tracker id')->unMorph();
        }

        if ($bencodeElement->exists('failure reason')) {
            $this->failureReason = $bencodeElement->get('failure reason')->unMorph();
        }

        if ($bencodeElement->exists('warning message')) {
            $this->warningMessage = $bencodeElement->get('warning message')->unMorph();
        }

        if ($bencodeElement->exists('min interval')) {
            $this->minimumInterval = $bencodeElement->get('min interval')->unMorph();
        }

        $peers = $bencodeElement->get('peers');

        if ($peers instanceof BencodeString) {
            foreach (str_split($peers->unMorph(), 6) as $item) {
                $this->peers[] = Peer::parse($item);
            }
        } else if ($peers instanceof BencodeList) {
            foreach ($peers->unMorph() as $item) {
                /** @var BencodeDictionary $item */
                $this->peers[] = Peer::parse($item);
            }
        }
    }

    public function encode() {
        assert(is_integer($this->interval));
        assert(is_integer($this->complete));
        assert(is_integer($this->incomplete));
        assert(is_array($this->peers));

        $dict = [
            'interval'   => $this->interval,
            'complete'   => $this->complete,
            'incomplete' => $this->incomplete,
        ];

        if ($this->failureReason) {
            $dict['failure reason'] = $this->failureReason;
        }

        if ($this->warningMessage) {
            $dict['warning message'] = $this->warningMessage;
        }

        if ($this->minimumInterval) {
            $dict['min interval'] = $this->minimumInterval;
        }

        if ($this->trackerId) {
            $dict['tracker id'] = $this->trackerId;
        }

        $dict['peers'] = array_map(function (Peer $peer) {
            return $peer->unMorph($this->compact);
        }, $this->peers);

        return (new BencodeDictionary($dict))->encode();
    }

    public function __dump() {
        return [
            'interval'   => $this->interval,
            'peers'      => $this->peers,
            'complete'   => $this->complete,
            'incomplete' => $this->incomplete,
        ];
    }
}