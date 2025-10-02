<?php

namespace App\Support\Spotlight;

class SearchResult
{
    public function __construct(
        protected string $id,
        protected string $title,
        protected ?string $details = null,
        protected ?string $icon = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'details' => $this->details,
            'icon' => $this->icon,
        ];
    }
}
