<?php

declare(strict_types=1);

namespace Zodimo\PN\Net\Models\Instance;

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
     * @param array<InstanceTokenInterface<TOKENCOLOURSET>> $tokens
     */
    private function __construct(private string $id, private array $tokens) {}

    /**
     * @template _TOKENCOLOURSET
     *
     * @param array<InstanceTokenInterface<_TOKENCOLOURSET>> $intialTokens
     *
     * @return Place<_TOKENCOLOURSET>
     */
    public static function create(string $id, array $intialTokens): Place
    {
        return new self($id, $intialTokens);
    }

    public function canEnable(string $instanceId, ArcEnablementInterface $arcEnablement): bool
    {
        foreach ($this->getInstanceTokens($instanceId) as $token) {
            if ($arcEnablement->acceptsToken($token->unwrap())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ArcEnablementInterface<TOKENCOLOURSET> $arcEnablement
     *
     * @return Option<InstanceTokenInterface<TOKENCOLOURSET>>
     */
    public function pop(string $instanceId, ArcEnablementInterface $arcEnablement): Option
    {
        foreach ($this->getInstanceTokens($instanceId) as $index => $token) {
            if ($arcEnablement->acceptsToken($token->unwrap())) {
                // remove token
                unset($this->tokens[$index]);
                // reset token index
                $this->tokens = [...$this->tokens];

                return Option::some($token->unwrap());
            }
        }

        return Option::none();
    }

    /**
     * @param InstanceTokenInterface<TOKENCOLOURSET> $token
     */
    public function push(InstanceTokenInterface $token): void
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
     * @return array<InstanceTokenInterface<TOKENCOLOURSET>>
     */
    public function getInstanceTokens(string $instanceId): array
    {
        return array_filter($this->tokens, fn (InstanceTokenInterface $token) => $token->getInstanceId() == $instanceId);
    }

    /**
     * @return array<InstanceTokenInterface<TOKENCOLOURSET>>
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}
