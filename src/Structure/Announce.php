<?php

namespace nanodesu88\bencode\Structure;

use nanodesu88\bencode\Bencode;
use nanodesu88\bencode\BencodeDictionary;
use nanodesu88\bencode\BencodeList;
use nanodesu88\bencode\BencodeString;
use nanodesu88\bencode\Structure\Support\PeerInterface;

class Announce extends Bencode
{
    /**
     * @var int
     */
    public $interval;

    /**
     * @var PeerInterface
     */
    public $peers = [];

    /**
     * @var bool
     */
    public $compact = false;

    /**
     * @var int
     */
    public $complete = 0;

    /**
     * @var int
     */
    public $incomplete = 0;

    public function prepare()
    {
        parent::prepare();

        $this['interval'] = $this->interval;

        if ($this->compact) {
            $this['peers'] = new BencodeString();

            foreach ($this->peers as $peer) {
                $peerIp = explode('.', long2ip($peer->ip));

                $this['peers'] .= pack("C*", $peerIp[0], $peerIp[1], $peerIp[2], $peerIp[3]) . pack("n*", (int)$peer->port);
            }
        } else {
            $this['peers'] = new BencodeList();

            foreach ($this->peers as $peer) {
                $p = new BencodeDictionary();
                $p['ip'] = $peer->ip;
                $p['peer_id'] = $peer->peerId;
                $p['port'] = $peer->port;

                $this['peers'][] = $p;
            }

            $this['complete'] = $this->complete;
            $this['incomplete'] = $this->incomplete;
        }
    }
}