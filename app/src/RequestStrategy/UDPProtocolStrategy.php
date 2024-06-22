<?php

namespace App\RequestStrategy;

/*
 * documentation on how UDP tracker protocol works
 * https://xbtt.sourceforge.net/udp_tracker_protocol.html
 */

use App\Dto\AnnounceOutputDto;
use App\Dto\DecodedTorrentDataDto;
use App\Exception\TorrentException;
use Exception;

class UDPProtocolStrategy implements RequestStrategyInterface
{
    const  CONNECT_ACTION = 0;
    const ANNOUNCE_ACTION = 1;

    /**
     * Retrieves the number of seeders and peers for the specified torrent announce
     *
     * @param DecodedTorrentDataDto $torrentData
     * @param string $announce
     * @return AnnounceOutputDto|null
     */
    public function fetchAnnounceData(DecodedTorrentDataDto $torrentData, string $announce): ?AnnounceOutputDto
    {
        if (!str_starts_with($announce, 'udp')) {
            new TorrentException('unsupported announce format');
        }

        $infoHash = $torrentData->getInfoHash();
        $transactionId = random_int(0, 0xFFFFFFFF);

        try {
            $connectionId = $this->geConnectionId($announce, $transactionId);
            return $this->getAnnounce($connectionId, $transactionId, $announce, $infoHash);
        } catch (Exception $exception) {
            //todo куда-нибудь в логи пусть пишется
        }

        return null;
    }

    /**
     * This method initiates a UDP connection to a tracker and retrieves
     *  the connection ID required for further communication.
     *
     * @param string $announce
     * @param int $transactionId
     * @return int
     * @throws TorrentException
     */
    public function geConnectionId(string $announce, int $transactionId): int
    {
        // todo генерировать $connectionId
        // Choose a (random) connection ID. 64-bit integer
        $connectionId = 0x41727101980;

        $packet = pack('JNN', $connectionId, self::CONNECT_ACTION, $transactionId);
        $socket = stream_socket_client($announce, $errno, $errstr, 2);

        if (!$socket) {
            throw new TorrentException('Error reading response or reasonable response size ' . $errno . ' ' . $errstr);
        }

        stream_set_timeout($socket, 3);

        // Send the packet.
        fwrite($socket, $packet);

        // Receive the packet.
        $response = fread($socket, 16);

        if ($response === false || strlen($response) < 16) {
            throw new TorrentException('Error reading response or reasonable response size');
        }

        $response_data = unpack('Naction/Ntransaction_id/Jconnection_id', $response);

        if ($response_data['action'] === self::CONNECT_ACTION &&
            $response_data['transaction_id'] === $transactionId
        ) {
            return $response_data['connection_id'];
        }

        throw new TorrentException('Something went wrong while receiving data');
    }


    /**
     * This method establishes a UDP connection to a tracker, sends a request
     *  to fetch leechers and seeders counts, and parses the response to extract
     *  the counts.
     *
     * @param int $connectionId
     * @param int $transactionId
     * @param string $announce
     * @param string $infoHash
     * @return AnnounceOutputDto
     * @throws TorrentException
     * @throws \Random\RandomException
     */
    public function getAnnounce(
        int $connectionId,
        int $transactionId,
        string $announce,
        string $infoHash
    ): AnnounceOutputDto {
        //	20-byte string	peer_id
        $peer_id = '-PC0001-' . substr(md5(uniqid(mt_rand(), true)), 0, 12); // 20 байт

        $downloaded = 0;
        $left = 0; // Количество байт, которое осталось скачать
        $uploaded = 0;
        $event = 0; // 0: none,   1: completed,  2: started,   3: stopped
        $ip = 0;
        $key = random_int(0, 0xFFFFFFFF);
        $num_want = -1;
        $port = $this->getServerPort($announce);

        $packet = pack('JNN', $connectionId, self::ANNOUNCE_ACTION, $transactionId) .
            $infoHash . $peer_id .
            pack('J3N2N2n', $downloaded, $left, $uploaded, $event, $ip, $key, $num_want, $port);

        $socket = stream_socket_client($announce, $errno, $errstr, 2);

        if (!$socket) {
            throw new TorrentException('Error reading response or reasonable response size ' . $errno . ' ' . $errstr);
        }

        fwrite($socket, $packet);

        $response = fread($socket, 1024);
        if ($response === false || strlen($response) < 20) {
            throw new TorrentException('Error reading response or reasonable response size');
        }

        $response_data = unpack('Naction/Ntransaction_id/Ninterval/Nleechers/Nseeders', substr($response, 0, 20));

        if ($response_data['transaction_id'] === $transactionId &&
            $response_data['action'] === self::ANNOUNCE_ACTION
        ) {
            return new AnnounceOutputDto(
                $response_data['leechers'],
                $response_data['seeders'],
            );
        }

        throw new TorrentException('Something went wrong while receiving data');
    }

    /**
     * @param $url
     * @return int|null
     */
    private function getServerPort($url): ?int
    {
        $parsedUrl = parse_url($url);

        if (isset($parsedUrl['port'])) {
            return $parsedUrl['port'];
        }

        return null;
    }
}