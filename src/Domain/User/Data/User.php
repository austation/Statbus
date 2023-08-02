<?php

namespace App\Domain\User\Data;

use App\Domain\Player\Data\PlayerBadge;
use App\Enum\PermissionsFlags;
use Symfony\Component\OptionsResolver\OptionsResolver;

class User
{
    private ?string $ckey = null;

    private string $rank = 'Player';

    private int $flags = 0;

    private ?string $feedback = null;

    private array $roles = [];

    private ?PlayerBadge $badge;

    private ?string $source;

    public function __construct()
    {

    }

    public function setCkey(string $ckey): self
    {
        $this->ckey = $ckey;
        return $this;
    }

    public function getCkey(): ?string
    {
        return $this->ckey;
    }

    public function setRank(string $rank = 'Player'): self
    {
        $this->rank = $rank;
        $this->badge = PlayerBadge::fromRank($this->getCkey(), $rank);
        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setFlags(int $flags): self
    {
        $this->flags = $flags;
        $this->roles = [];
        foreach (PermissionsFlags::getArray() as $p => $b) {
            if ($flags & $b) {
                $this->roles[$p] = true;
            }
        }
        $this->roles = array_keys($this->roles);
        return $this;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function setFeedback(string|null $feedback): self
    {
        $this->feedback = $feedback;
        return $this;
    }

    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    public function getBadge(): ?PlayerBadge
    {
        return $this->badge;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public static function fromArray(array $data): self
    {
        $resolver = new OptionsResolver();
        $resolver
            ->define('ckey')
            ->allowedTypes('string')
            ->info('The ckey')

            ->define('rank')
            ->required()
            ->default('Player')
            ->allowedTypes('string', 'null')
            ->info('The rank of this player, defaults to Player')

            ->define('flags')
            ->required()
            ->default(0)
            ->allowedTypes('int', 'null')
            ->info('The player rank flags, if set. Defaults to 0')

            ->define('feedback')
            ->required()
            ->default(null)
            ->allowedTypes('string', 'null')
            ->info('The player feedback thread link');

        $data = $resolver->resolve($data);
        $user = new self();
        $user->setCkey($data['ckey']);
        $user->setRank($data['rank'] ?: 'Player');
        $user->setFlags($data['flags'] ?: 0);
        $user->setFeedback($data['feedback']);
        return $user;
    }

    public function has(string $key): bool
    {
        return in_array($key, $this->getRoles());
    }

}
