<?php

namespace App\Domain\Player\Data;

use App\Enum\AdminRanks;
use App\Service\LuminosityContrast;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerBadge
{
    private string $ckey;

    private string $backColor = '#ccc';

    private string $foreColor = '#000';

    private string $icon = 'user';

    private string $title = 'Player';

    public function __construct(string $ckey, array $options = [])
    {
        $this->setCkey($ckey);
        $this->setBackColor($options['backColor']);
        if(!isset($options['foreColor'])) {
            $this->setColor($options['backColor']);
        } else {
            $this->setForeColor($options['foreColor']);
        }
        $this->setIcon($options['icon']);
        $this->setTitle($options['title']);
    }

    public function setCkey(string $ckey): self
    {
        $this->ckey = $ckey;
        return $this;
    }

    public function getCkey(): string
    {
        return $this->ckey;
    }

    public function setColor(string $color): self
    {
        $this->setBackColor($color);
        $this->setForeColor(LuminosityContrast::getContrastColor($color));
        return $this;
    }

    public static function fromArray(string $ckey, array $data): self
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'backColor' => '#ccc',
            'foreColor' => '#000',
            'icon'      => 'user',
            'title'     => 'player',
        ]);
        $data = $resolver->resolve($data);
        $badge = new self($ckey, $data);

        return $badge;
    }

    public static function fromRank(string $ckey, string $rank = 'Player'): self
    {
        $data = AdminRanks::getRankInfo($rank);
        $data['title'] = $rank;
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'backColor' => '#ccc',
            'foreColor' => '#000',
            'icon'      => 'user',
            'title'     => 'Player',
        ]);
        $data = $resolver->resolve($data);
        return new self($ckey, $data);
    }

    public function setBackColor(string $color): self
    {
        $this->backColor = $color;
        return $this;
    }

    public function getBackColor(): string
    {
        return $this->backColor;
    }

    public function setForeColor(string $color): self
    {
        $this->foreColor = $color;
        return $this;
    }

    public function getForeColor(): string
    {
        return $this->foreColor;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStyle(): string
    {
        return sprintf("background:%s; color: %s;", $this->getBackColor(), $this->getForeColor());
    }
}
