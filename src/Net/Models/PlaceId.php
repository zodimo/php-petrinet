<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

/**
 * @template TOKENCOLOURSET
 */
class PlaceId
{
    /**
     * @param ?TOKENCOLOURSET $phantomTokenType
     */
    private function __construct(
        private string $id,
        // @phpstan-ignore property.onlyWritten
        private $phantomTokenType = null
    ) {}

    /**
     * @param ?TOKENCOLOURSET $phantomTokenType
     *
     * @return PlaceId<TOKENCOLOURSET>
     */
    public static function create(string $id, $phantomTokenType = null)
    {
        return new self($id, $phantomTokenType);
    }

    public function getId(): string
    {
        return $this->id;
    }
}
