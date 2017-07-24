<?php

namespace nanodesu88\bencode\Structure\Support;

/**
 * @property string $ip
 * @property string $peerId
 * @property int $port
 */
interface PeerInterface
{
    /**
     * @return string
     */
    public function getIP();

    /**
     * @return string
     */
    public function getPeerId();

    /**
     * @return int
     */
    public function getPort();
}