<?php

namespace Application\Model;

class Track
{
    private ?string $title;

    private ?string $duration;

    public function __construct(
        ?string $title = null,
        ?string $duration = null
    ) {
        $this->title    = $title;
        $this->duration = $duration;
    }

    public function getTitle() : ?string
    {
        return $this->title;
    }

    public function getDuration() : ?string
    {
        return $this->duration;
    }
}
