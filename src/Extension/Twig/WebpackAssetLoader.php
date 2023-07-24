<?php

namespace App\Extension\Twig;

use Random\Randomizer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;

class WebpackAssetLoader extends AbstractExtension
{
    private $entrypointLookup;

    public function __construct(private array $settings)
    {
        $this->entrypointLookup = new EntrypointLookup(__DIR__."/../../../public/build/entrypoints.json");
        var_dump($this->settings);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('webpack_entry_link_tags', [$this, 'renderWebpackLinkTags'], ['is_safe' => ['html']]),
            new TwigFunction('webpack_entry_script_tags', [$this, 'renderWebpackScriptTags'], ['is_safe' => ['html']])
        ];
    }

    public function renderWebpackLinkTags()
    {
        $tags = '';
        $randomizer = new Randomizer();

        foreach ($this->entrypointLookup->getCssFiles('app') as $entry) {
            $rand = '';
            if($this->settings['debug']) {
                $rand = "?".bin2hex($randomizer->getBytes(8));
            }
            $tags .= sprintf("<link href='%s%s' rel='stylesheet' /> ", $entry, $rand);
        }
        return $tags;
    }

    public function renderWebpackScriptTags()
    {
        $tags = '';
        $randomizer = new Randomizer();

        foreach ($this->entrypointLookup->getJavaScriptFiles('app') as $entry) {
            $rand = '';
            if($this->settings['debug']) {
                $rand = "?".bin2hex($randomizer->getBytes(8));
            }
            $tags .= sprintf("<script src='%s%s' defer></script>", $entry, $rand);
        }
        return $tags;
    }
}
