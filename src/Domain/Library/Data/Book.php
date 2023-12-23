<?php

namespace App\Domain\Library\Data;

use App\Domain\Player\Data\PlayerBadge;
use App\Service\HTMLSanitizerService;
use DateTime;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class Book
{
    public ?PlayerBadge $authorBadge;

    public function __construct(
        public int $id,
        public string $author,
        public string $title,
        public string $content,
        public string $category,
        public ?string $ckey,
        public DateTime $dateTime,
        public ?bool $deleted,
        public ?int $round,
        public ?string $a_rank
    ) {
        $this->sanitizeContent();
        $this->setAuthorBadge();
    }

    public function redactAuthor(): self
    {
        $this->ckey = null;
        $this->a_rank = 'Player';
        $this->authorBadge = null;
        return $this;
    }

    public function isDeleted(): bool
    {
        return (bool) $this->deleted;
    }

    private function sanitizeContent(): self
    {

        $content =  html_entity_decode($this->content);

        $cmconfig = [];
        $environment = new Environment($cmconfig);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $converter = new MarkdownConverter($environment);
        

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('AutoFormat.Linkify', true);
        $config->set('HTML.Allowed', 'br, hr, a[href], font[color], b, h1, h2, h3, h4, h5, em, i, blockquote, ul, ol, li, B, BR, U, HR');
        $config->set('HTML.TargetBlank', true);

        $content = HTMLSanitizerService::sanitizeStringWithConfig($config, $content);
        $content = $converter->convert($content);
        $this->content = nl2br($content, false);
        return $this;
    }

    private function setAuthorBadge(): self
    {
        $this->authorBadge = PlayerBadge::fromRank($this->ckey, $this->a_rank);
        return $this;
    }

}
