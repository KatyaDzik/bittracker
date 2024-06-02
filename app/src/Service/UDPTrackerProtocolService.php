<?php

namespace App\Service;

/*
 * documentation on how UDP tracker protocol works
 * https://xbtt.sourceforge.net/udp_tracker_protocol.html
 */

use App\Dto\AnnounceOutputDto;
use App\Dto\DecodedTorrentDataDto;
use App\Exception\TorrentException;

class UDPTrackerProtocolService
{
    const  CONNECT_ACTION = 0;
    const ANNOUNCE_ACTION = 1;

    /**
     * @throws TorrentException
     */
    public function __construct(DecodedTorrentDataDto $dataDto)
    {
        $announce = $dataDto->getAnnounce();
        $infoHash = $dataDto->getInfoHash();

        // Choose a (random) transaction ID. 32-bit integer
        $transactionId = random_int(0, 0xFFFFFFFF);

        $connectionId = $this->geConnectionId($announce, $transactionId);
        $announceData = $this->getAnnounce($connectionId, $transactionId, $announce, $infoHash);
        dd($announceData);
    }

    /*
     * Obtain a connection ID.
     */
    /**
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
     * @throws TorrentException
     * @throws RandomException
     */
    public function getAnnounce(int $connectionId, int $transactionId, string $announce, string $infoHash): AnnounceOutputDto
    {
        //	20-byte string	peer_id
        $peer_id = '-PC0001-' . substr(md5(uniqid(mt_rand(), true)), 0, 12); // 20 байт

        $downloaded = 0;
        $left = 0; // Количество байт, которое осталось скачать
        $uploaded = 0;
        $event = 0; // 0: none,   1: completed,  2: started,   3: stopped
        $ip = 0;
        $key = random_int(0, 0xFFFFFFFF);
        $num_want = -1;
        //todo получить из $announce
        $port = 6969;

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

        if ($response_data['transaction_id']  === $transactionId &&
            $response_data['action'] === self::ANNOUNCE_ACTION
        ) {
            return new AnnounceOutputDto(
                $response_data['action'],
                $response_data['transaction_id'],
                $response_data['interval'],
                $response_data['leechers'],
                $response_data['seeders'],
            );
        }

        throw new TorrentException('Something went wrong while receiving data');
    }
}