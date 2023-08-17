<?php

namespace App\Service;

use HTMLPurifier;

class HTMLSanitizerService
{
    public $purifier;

    public function __construct($config = [])
    {
        require_once __DIR__ . '/../../vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';
        if (!$config) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('AutoFormat.Linkify', true);
            $config->set('HTML.Allowed', 'br, hr, a[href]');
            $config->set('HTML.TargetBlank', true);
            $config->set('URI.HostBlacklist', '');
        }
        $this->purifier = new HTMLPurifier($config);
    }

    public function sanitizeString(string $string): string
    {
        $string = preg_replace('/(href=\'\?[\S]+\')/', '', $string);
        return $this->purifier->purify($string);
    }
}
