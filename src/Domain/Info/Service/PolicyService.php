<?php


namespace App\Domain\Info\Service;

use App\Service\ServerInformationService;

class PolicyService
{

    public function __construct(
        private ServerInformationService $serverService
    ) {
    }


    public function getServers(): array
    {
        return ServerInformationService::getServerInfo();
    }
}
