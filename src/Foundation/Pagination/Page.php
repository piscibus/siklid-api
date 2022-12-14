<?php

declare(strict_types=1);

namespace App\Foundation\Pagination;

use App\Foundation\Pagination\Contract\PageInterface;
use JsonSerializable;

/**
 * @template T The type of the elements of the page.
 */
final class Page implements PageInterface, JsonSerializable
{
    /**
     * @var array|T[] the elements of the page
     */
    private array $data = [];

    private array $links = [];

    private array $meta = [];

    public function getData(): array
    {
        return $this->data;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public static function init(): self
    {
        return new self();
    }

    /**
     * @param array|T[] $data
     *
     * @return $this
     */
    public function data(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function links(array $links): self
    {
        $this->links = $links;

        return $this;
    }

    public function meta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'data' => $this->data,
            'meta' => $this->meta,
            'links' => $this->links,
        ];
    }
}
