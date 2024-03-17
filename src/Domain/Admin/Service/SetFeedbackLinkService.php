<?php

namespace App\Domain\Admin\Service;

use App\Domain\Admin\Repository\AdminRepository;
use Psr\Container\ContainerInterface;
use DI\Attribute\Inject;

class SetFeedbackLinkService
{
    private ?string $regex = null;

    #[Inject]
    private AdminRepository $adminRepository;

    public function __construct(private ContainerInterface $container)
    {
        $this->regex = $this->container->get('settings')['app']['feedback_regex'];
    }

    public function setFeedbackUrl(string $url, string $ckey): bool|string
    {
        $verified = $this->verifyLinkRegex($url);
        if(true === $verified) {
            return $this->adminRepository->updateFeedbackURL($url, $ckey);
        }
        return $verified;
    }

    public function verifyLinkRegex(string $url): bool
    {
        if(1 === preg_match('/'.$this->regex.'/', $url)) {
            return true;
        }
        return false;
    }

}
