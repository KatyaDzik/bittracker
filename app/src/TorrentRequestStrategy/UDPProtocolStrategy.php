<?php

namespace App\TorrentRequestStrategy;

/*
 * documentation on how UDP tracker protocol works
 * https://xbtt.sourceforge.net/udp_tracker_protocol.html
 */

use App\Dto\AnnounceOutputDto;
use App\Dto\DecodedTorrentDataDto;
use App\Exception\TorrentException;
use Exception;
use Psr\Log\LoggerInterface;

class UDPProtocolStrategy implements RequestStrategyInterface
{
    public function __construct(
        protected readonly LoggerInterface $torrentLogger,
    ) {
    }

    const  CONNECT_ACTION = 0;
    const SCRAPE_ACTION = 2;

    /**
     * Retrieves the number of seeders and peers for the specified torrent announce
     *
     * @param DecodedTorrentDataDto $torrentData
     * @param string $announce
     * @return AnnounceOutputDto|null
     */
    public function fetchScrapeData(DecodedTorrentDataDto $torrentData, string $announce): ?AnnounceOutputDto
    {
        if (!str_starts_with($announce, 'udp')) {
            new TorrentException('unsupported announce format');
        }

        $infoHash = $torrentData->getInfoHash();

        try {
            return $this->getScrapeData($announce, $infoHash);
        } catch (Exception $exception) {
            $this->torrentLogger->error($exception->getMessage());
        }

        return null;
    }

    /**
     * This method establishes a socket connection to a given tracker URL.
     * It first sends a packet to obtain the connectionId required for further communication.
     * Once the connectionId is received, the method then sends a scrape request packet
     * to retrieve statistics about the torrent, including the number of seeders and leechers.
     *
     * @param string $announce
     * @param string $infoHash
     * @return AnnounceOutputDto
     * @throws TorrentException
     * @throws \Random\RandomException
     */
    public function getScrapeData(string $announce, string $infoHash): AnnounceOutputDto
    {
        $transactionId = random_int(0, 0xFFFFFFFF);
        $connectionId = 0x41727101980;
        $connectionPacket = pack('JNN', $connectionId, self::CONNECT_ACTION, $transactionId);
        $socket = stream_socket_client($announce, $errno, $errstr, 2);

        if (!$socket) {
            throw new TorrentException('Error reading response or reasonable response size ' . $errno . ' ' . $errstr);
        }

        stream_set_timeout($socket, 2);
        fwrite($socket, $connectionPacket);
        $response = fread($socket, 16);

        if ($response === false || strlen($response) < 16) {
            throw new TorrentException('Error reading response or reasonable response size');
        }

        $responseData = unpack('Naction/Ntransaction_id/Jconnection_id', $response);

        if ($responseData['action'] !== self::CONNECT_ACTION ||
            $responseData['transaction_id'] !== $transactionId
        ) {
            throw new TorrentException('Something went wrong while receiving data');
        }

        $connectionId = $responseData['connection_id'];

        $scrapePacket = pack(
            'JNNa20',
            $connectionId,
            self::SCRAPE_ACTION,
            $transactionId,
            hex2bin($infoHash)
        );

        fwrite($socket, $scrapePacket);
        $response = fread($socket, 1024);

        if ($response === false || strlen($response) < 8) {
            throw new TorrentException('Error reading response or reasonable response size');
        }

        $responseData = unpack('Naction/Ntransaction_id', substr($response, 0, 8));

        if ($responseData['transaction_id'] !== $transactionId || $responseData['action'] !== self::SCRAPE_ACTION) {
            throw new TorrentException('Something went wrong while receiving data');
        }

        $torrentData = unpack('Nseeders/Ncompleted/Nleechers', substr($response, 8, 12));

        return new AnnounceOutputDto(
            $torrentData['leechers'],
            $torrentData['seeders'],
        );
    }
}