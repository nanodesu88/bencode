<?php

namespace nanodesu88\bencode\Structure\Support;

/**
 * @property-read string $ip
 * @property-read string $peerId
 * @property-read int $port
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