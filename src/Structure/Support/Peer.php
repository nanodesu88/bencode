<?php

namespace nanodesu88\bencode\Structure\Support;

use nanodesu88\bencode\BencodeDictionary;

class Peer implements PeerInterface
{
    public $ip;
    public $peerId;
    public $port;

    public static function parse($value)
    {
        $peer = new static();

        if ($value instanceof BencodeDictionary) {
            $peer->ip = $value->getValue('ip')->getValue();
            $peer->peerId = $value->getValue('peer id')->getValue();
            $peer->port = $value->getValue('port')->getValue();
        } else if (is_string($value)) {
            $peer->ip = unpack('C', $value[0])[1] . '.' . unpack('C', $value[1])[1] . '.' . unpack('C', $value[2])[1] . '.' . unpack('C', $value[3])[1];
            $peer->port = unpack('n', $value[4] . $value[5])[1];
        }

        return $peer;
    }

    /**
     * @inheritdoc
     */
    public function getIP()
    {
        return $this->ip;
    }

    /**
     * @inheritdoc
     */
    public function getPeerId()
    {
        return $this->peerId;
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->getPort();
    }
}