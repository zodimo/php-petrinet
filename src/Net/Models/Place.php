<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models;

use Zodimo\BaseReturn\Option;

/**
 * @template TOKENCOLOUR
 *
 * @template-implements InputPlaceInterface<TOKENCOLOUR>
 * @template-implements OutputPlaceInterface<TOKENCOLOUR>
 */
class Place implements InputPlaceInterface, OutputPlaceInterface
{
    /**
     * @param array<TOKENCOLOUR> $tokens
     */
    private function __construct(private string $id, private array $tokens) {}

    /**
     * @template _TOKENCOLOUR
     *
     * @param array<_TOKENCOLOUR> $intialTokens
     *
     * @return Place<_TOKENCOLOUR>
     */
    public static function create(string $id, array $intialTokens): Place
    {
        return new self($id, $intialTokens);
    }

    public function canEnable(ArcEnablementInterface $arcEnablement): bool
    {
        foreach ($this->tokens as $token) {
            if ($arcEnablement->acceptsToken($token)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ArcEnablementInterface<TOKENCOLOUR> $arcEnablement
     *
     * @return Option<TOKENCOLOUR>
     */
    public function pop(ArcEnablementInterface $arcEnablement): Option
    {
        foreach ($this->tokens as $index => $token) {
            if ($arcEnablement->acceptsToken($token)) {
                unset($this->tokens[$index]);

                return Option::some($token);
            }
        }

        return Option::none();
    }

    /**
     * @param TOKENCOLOUR $token
     */
    public function push($token): void
    {
        $this->tokens[] = $token;
    }

    /**
     * @return PlaceId<TOKENCOLOUR>
     */
    public function getPlaceId(): PlaceId
    {
        return PlaceId::create($this->id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array<TOKENCOLOUR>
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}
