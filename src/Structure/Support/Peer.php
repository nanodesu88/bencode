<?php

namespace nanodesu88\bencode\Structure\Support;

use nanodesu88\bencode\BencodeDictionary;
use nanodesu88\bencode\BencodeException;
use nanodesu88\bencode\BencodeString;

class Peer
{
    public $ip;
    public $peerId;
    public $port;

    public function __construct(string $ip, int $port, string $peerId = null) {
        assert($peerId === null || strlen($peerId) === 20);

        [$this->ip, $this->port, $this->peerId] = [$ip, $port, $peerId];
    }

    /**
     * @param string|array|BencodeString|BencodeDictionary $value
     * @return static
     * @throws BencodeException
     */
    public static function parse($value) {
        if ($value instanceof BencodeDictionary) {
            [$ip, $peerId, $port] = [$value->get('ip')->unMorph(), $value->get('peer id')->unMorph(), $value->get('port')->unMorph()];

            return new static($ip, $port, $peerId);
        } else if (is_string($value) || $value instanceof BencodeString) {
            if ($value instanceof BencodeString) {
                $value = $value->unMorph();
            }

            [$ip, $port] = [unpack('C', $value[0])[1] . '.' . unpack('C', $value[1])[1] . '.' . unpack('C', $value[2])[1] . '.' . unpack('C', $value[3])[1], unpack('n', $value[4] . $value[5])[1]];

            return new static($ip, $port);
        } else if (is_array($value)) {
            return new static($value['ip'], $value['port'], $value['peer id']);
        }

        throw new BencodeException();
    }

    /**
     * @inheritDoc
     */
    public function unMorph(bool $compact) {
        if ($compact) {
            $ipBytes = explode('.', $this->ip);

            return pack('C', $ipBytes[0]) . pack('C', $ipBytes[1]) . pack('C', $ipBytes[2]) . pack('C', $ipBytes[3]) . pack('n', $this->port);
        } else {
            return [
                'peer id' => $this->peerId,
                'ip'      => $this->ip,
                'port'    => $this->port,
            ];
        }
    }


    /**
     * @inheritdoc
     */
    public function getIP() {
        return $this->ip;
    }

    /**
     * @inheritdoc
     */
    public function getPeerId() {
        return $this->peerId;
    }

    /**
     * @inheritdoc
     */
    public function getPort() {
        return $this->getPort();
    }
}