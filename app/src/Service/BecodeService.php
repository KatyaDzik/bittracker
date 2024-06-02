<?php

namespace App\Service;

use App\Dto\DecodedTorrentDataDto;
use DateTime;
use Symfony\Component\HttpFoundation\File\File;

class BecodeService
{
    private string $fileContent;
    private int $position = 0;

    private ?DecodedTorrentDataDto $decodedTorrentData;

    public function __construct(File $file)
    {
        $this->fileContent = $file->getContent();
        $decodedData = $this->bdecode();

        $this->decodedTorrentData = new DecodedTorrentDataDto(
            announce: $decodedData['announce'],
            announceList: $decodedData['announce-list'] ?? null,
            comment: $decodedData['comment'] ?? null,
            createdBy: $decodedData['created by'] ?? null,
            creationDate: $decodedData['creation date'] ? (new DateTime())->setTimestamp($decodedData['creation date']) : null,
            encoding: $decodedData['encoding'] ?? null,
            length:  $decodedData['info']['length'],
            name: $decodedData['info']['name'],
            pieceLength: $decodedData['info']['piece length'],
            pieces: $decodedData['info']['pieces'],
            publisher: $decodedData['publisher'] ?? null,
            publisherUrl: $decodedData['publisher-url'] ?? null,
            infoHash: $this->getInfoHash($decodedData),
        );
    }

    protected function getChar(): string
    {
        if (!$this->fileContent ) {
            return false;
        }

        return substr($this->fileContent, $this->position, 1);
    }

    protected function bdecode(): string|int|array
    {
        $char = $this->getChar();

        switch ($char) {
            case 'i':
                $this->position++;
                return $this->decodeInt();
            case 'l':
                $this->position++;
                return $this->decodeList();
            case 'd':
                $this->position++;
                return $this->decodeDictionary();
            default:
                return $this->decodeString();
        }
    }

    /**
     * Decode a Becoded dictionary
     *
     * Dictionary entries begin with the prefix "d" and end with the character "e".
     * They resemble lists, except that elements are in key-value pairs.
     * For instance, the dictionary {"name":"John", "age":25, "city":"New York"}
     * would be encoded in bEncode format as d4:name4:John3:agei25e4:city8:New Yorke.
     *
     */
    protected function decodeDictionary(): array
    {
        $return = array();

        while ($char = $this->getChar()) {
            if ($char == 'e') {
                break;
            }

            $key = $this->decodeString();

            $val = $this->bdecode();
            $return[$key] = $val;
        }

        $this->position++;

        return $return;
    }

    /**
     * Decode a BEncoded list
     *
     * Lists are stored as l[value 1][value2][value3][...]e. For example, {spam, eggs, cheeseburger} is stored as:
     * l4:
     *
     * @return array
     */
    protected function decodeList(): array
    {
        $return = array();

        while (substr($this->fileContent, $this->position, 1) != 'e') {
            $val = $this->bdecode();

            $return[] = $val;
        }

        $this->position++;

        return $return;
    }

    /**
     * Decode a BEncoded string
     *
     * Strings are prefixed with their length followed by a colon.
     * For example, "Pupok" would bEncode to 5:Pupok
     *
     */
    protected function decodeString(): string
    {
        $posColon = strpos($this->fileContent, ':', $this->position);
        $strLength = intval(substr($this->fileContent, $this->position, $posColon));

        if ($strLength === 0) {
            $return = '';
        } else {
            $return = substr($this->fileContent, $posColon + 1, $strLength);
        }

        $this->position = $posColon + $strLength + 1;

        return $return;
    }

    /**
     * Decode a BEncoded integer
     *
     * Integers are prefixed with an i and terminated by an e. For
     * example, 123 would bEcode to i123e, -3272002 would bEncode to
     * i-3272002e.
     *
     */
    protected function decodeInt(): int
    {
        $pos_e  = strpos($this->fileContent, 'e', $this->position);

        $return = intval(substr($this->fileContent, $this->position, $pos_e - $this->position));
        $this->position = $pos_e + 1;

        return $return;
    }

    public function getDecodedTorrentData(): decodedTorrentDataDto
    {
        return $this->decodedTorrentData;
    }

    function getInfoHash(array $decodedData, $raw = false): string
    {
        $Encoder = new Encode();

        return sha1($Encoder->encode($decodedData['info']), $raw);
    }
}