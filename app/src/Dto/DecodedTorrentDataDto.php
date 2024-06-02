<?php

namespace App\Dto;

use DateTimeInterface;

class DecodedTorrentDataDto
{
    /**
     * DecodedTorrentDataDto constructor.
     *
     * @param string $announce The URL of the tracker for the torrent.
     * @param array|null $announceList A listing of the URLs of alternate trackers for the torrent. The URLs are divided
     *                                 into groups (each is a list). Trackers in each group may be shuffled, and groups
     *                                 are processed in the order they appear. Optional.
     * @param string|null $comment A comment about the torrent. Optional.
     * @param string|null $createdBy The name and version of the program used to create the torrent. Optional.
     * @param DateTimeInterface|null $creationDate The creation date of the torrent in Unix epoch format. Optional.
     * @param string $encoding The encoding used for the torrent file (e.g., UTF-8).
     * @param string $length The length of the file in bytes.
     * @param string $name The name of the file or directory represented by the torrent.
     * @param string $pieceLength The number of bytes in each piece. Files in torrents are divided into "pieces" with a
     *                            specific length.
     * @param string $pieces A (byte) string consisting of the concatenation of all 20-byte SHA1 hash values, one per piece.
     * @param string|null $publisher The publisher of the torrent. Optional.
     * @param string|null $publisherUrl The URL of the publisher's website. Optional.
     */
    public function __construct(
        private readonly string             $announce,
        private readonly ?array             $announceList,
        private readonly ?string            $comment,
        private readonly ?string            $createdBy,
        private readonly ?DateTimeInterface $creationDate,
        private readonly ?string             $encoding,
        private readonly string             $length,
        private readonly string             $name,
        private readonly string             $pieceLength,
        private readonly string             $pieces,
        private readonly ?string            $publisher,
        private readonly ?string            $publisherUrl,
        private readonly ?string            $infoHash,
    ) {
    }

    public function getAnnounce(): string
    {
        return $this->announce;
    }

    public function getAnnounceList(): ?array
    {
        return $this->announceList;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function getCreationDate(): ?DateTimeInterface
    {
        return $this->creationDate;
    }

    public function getEncoding(): ?string
    {
        return $this->encoding;
    }

    public function getLength(): string
    {
        return $this->length;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPieceLength(): string
    {
        return $this->pieceLength;
    }

    public function getPieces(): string
    {
        return $this->pieces;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function getPublisherUrl(): ?string
    {
        return $this->publisherUrl;
    }

    public function getInfoHash(): ?string
    {
        return $this->infoHash;
    }
}