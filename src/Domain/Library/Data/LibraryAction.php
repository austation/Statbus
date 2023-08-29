<?php

namespace App\Domain\Library\Data;

use App\Domain\Library\Data\ActionEnum;
use App\Domain\Player\Data\PlayerBadge;
use DateTime;

class LibraryAction
{
    public PlayerBadge $badge;

    public function __construct(
        public int $id,
        public int $book,
        public string $reason,
        public string $ckey,
        public DateTime $datetime,
        public $action,
        public string $rank
    ) {
        $this->setbadge();
        $this->action = ActionEnum::tryFrom($this->action);
    }

    private function setBadge(): self
    {
        $this->badge = PlayerBadge::fromRank($this->ckey, $this->rank);
        return $this;
    }

}
