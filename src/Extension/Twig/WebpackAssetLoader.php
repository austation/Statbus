<?php

namespace App\Extension\Twig;

use Random\Randomizer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookup;

class WebpackAssetLoader extends AbstractExtension
{
    private $entrypointLookup;
    private $hashes = [];
    public function __construct(private array $settings)
    {
        $this->entrypointLookup = new EntrypointLookup(__DIR__."/../../../public/build/entrypoints.json");
        $this->hashes = $this->entrypointLookup->getIntegrityData();

    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('webpack_entry_link_tags', [$this, 'renderWebpackLinkTags'], ['is_safe' => ['html']]),
            new TwigFunction('webpack_entry_script_tags', [$this, 'renderWebpackScriptTags'], ['is_safe' => ['html']])
        ];
    }

    public function renderWebpackLinkTags(string $module = 'app')
    {
        $tags = '';
        $randomizer = new Randomizer();
        foreach ($this->entrypointLookup->getCssFiles($module) as $entry) {
            $rand = '';
            if($this->settings['debug']) {
                $rand = "?".bin2hex($randomizer->getBytes(8));
            }
            $integrity = '';
            if(isset($this->hashes[$entry])) {
                $integrity = "integrity='".$this->hashes[$entry]."'";
            }
            $tags .= sprintf("<link href='%s%s' rel='stylesheet' %s /> ", $entry, $rand, $integrity);
        }
        return $tags;
    }

    public function renderWebpackScriptTags(string $module = 'app')
    {
        $tags = '';
        $randomizer = new Randomizer();
        foreach ($this->entrypointLookup->getJavaScriptFiles($module) as $entry) {
            $rand = '';
            if($this->settings['debug']) {
                $rand = "?".bin2hex($randomizer->getBytes(8));
            }
            $integrity = '';
            if(isset($this->hashes[$entry])) {
                $integrity = "integrity='".$this->hashes[$entry]."'";
            }
            $tags .= sprintf("<script src='%s%s' %s defer></script>\r", $entry, $rand, $integrity);
        }
        return $tags;
    }
}
