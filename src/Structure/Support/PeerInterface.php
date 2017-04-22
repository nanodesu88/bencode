<?php

namespace nanodesu88\bencode\Structure\Support;

/**
 * @property-read string $ip
 * @property-read string $peerId
 * @property-read int $port
 */
interface PeerInterface
{
    public function getIP();

    public function getPeerId();

    public function getPort();
}