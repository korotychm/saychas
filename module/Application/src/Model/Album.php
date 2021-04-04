<?php

namespace Application\Model;

class Album
{
    private ?string $title;

    private ?string $artist;

    private array $tracks;

    public function __construct(
        ?string $title = null,
        ?string $artist = null,
        array $tracks = []
    ) {
        $this->title  = $title;
        $this->artist = $artist;
        $this->tracks = $tracks;
    }

    public function getTitle() : ?string
    {
        return $this->title;
    }

    public function getArtist() : ?string
    {
        return $this->artist;
    }

    public function getTracks() : array
    {
        return $this->tracks;
    }
}
