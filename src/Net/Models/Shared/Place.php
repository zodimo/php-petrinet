<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Shared;

use Zodimo\BaseReturn\Option;
use Zodimo\PN\Net\Models\ArcEnablementInterface;
use Zodimo\PN\Net\Models\PlaceId;

/**
 * @template TOKENCOLOURSET
 *
 * @template-implements InputPlaceInterface<TOKENCOLOURSET>
 * @template-implements OutputPlaceInterface<TOKENCOLOURSET>
 */
class Place implements InputPlaceInterface, OutputPlaceInterface
{
    /**
     * @param array<TOKENCOLOURSET> $tokens
     */
    private function __construct(private string $id, private array $tokens) {}

    /**
     * @template _TOKENCOLOURSET
     *
     * @param array<_TOKENCOLOURSET> $intialTokens
     *
     * @return Place<_TOKENCOLOURSET>
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
     * @param ArcEnablementInterface<TOKENCOLOURSET> $arcEnablement
     *
     * @return Option<TOKENCOLOURSET>
     */
    public function pop(ArcEnablementInterface $arcEnablement): Option
    {
        foreach ($this->tokens as $index => $token) {
            if ($arcEnablement->acceptsToken($token)) {
                // remove token
                unset($this->tokens[$index]);
                // reset token index
                $this->tokens = [...$this->tokens];

                return Option::some($token);
            }
        }

        return Option::none();
    }

    /**
     * @param TOKENCOLOURSET $token
     */
    public function push($token): void
    {
        /**
         * runtime type check or is phpstan enough ?
         */
        $this->tokens[] = $token;
    }

    /**
     * @return PlaceId<TOKENCOLOURSET>
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
     * @return array<TOKENCOLOURSET>
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}
