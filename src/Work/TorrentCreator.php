<?php

namespace nanodesu88\bencode\Work;

use Illuminate\Filesystem\Filesystem;
use nanodesu88\bencode\BencodeDictionary;
use nanodesu88\bencode\Helper\ContiniousStream;
use nanodesu88\bencode\Structure\Torrent;
use nanodesu88\bencode\Work\Creator\DirectoryStream;
use nanodesu88\bencode\Work\Creator\PathNotFoundException;

class TorrentCreator
{
    /**
     * @param string $path
     * @param int    $pieceLength
     * @param bool   $private
     * @throws PathNotFoundException
     */
    public function create(string $path, int $pieceLength = 16384, bool $private = false) {
        if (!file_exists($path)) {
            throw new PathNotFoundException();
        }

        $multiple = is_dir($path);

        $dictionary = [
            'info'     => [],
            'announce' => '',
        ];

        $dictionary['info']['piece length'] = $pieceLength;
        $dictionary['info']['pieces']       = '';

        if ($private) {
            $dictionary['info']['private'] = (int)$private;
        }

        if (is_dir($path)) {
            $dictionary['info']['name']  = basename($path);
            $dictionary['info']['files'] = [];

            $files = (new Filesystem())->allFiles($path);

            foreach ($files as $fileInfo) {
                $dictionary['info']['files'][] = [
                    'length' => $filesize = filesize($fileInfo->getPathname()),
                    'path'   => explode(DIRECTORY_SEPARATOR, str_ireplace('\\', DIRECTORY_SEPARATOR, $fileInfo->getRelativePathname())),
                ];
            }

            $stream = new ContiniousStream(new DirectoryStream($files));

            while ($pieceContent = $stream->read($pieceLength)) {
                $dictionary['info']['pieces'] .= sha1($pieceContent, true);
            }
        } else {
            $length = filesize($path);
            $handle = fopen($path, 'r');

            for ($i = 0; $i < $length; $i += $pieceLength) {
                $pieceContent = fread($handle, $pieceLength);

                $dictionary['info']['pieces'] .= sha1($pieceContent, true);
            }

            fclose($handle);

            $dictionary['info']['name']   = basename($path);
            $dictionary['info']['length'] = $length;
            // $dictionary['info']['md5sum'] = md5_file($path);
        }

        $structure = new Torrent();
        $structure->load(new BencodeDictionary($dictionary));

        return $structure;
    }
}
