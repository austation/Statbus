<?php

namespace App\Controller\Info;

use App\Controller\Controller;
use App\Domain\Admin\Repository\AdminLogRepository;
use App\Domain\Admin\Repository\AdminRepository;
use App\Enum\PermissionsFlags;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class AdminLogController extends Controller
{
    #[Inject]
    private AdminLogRepository $adminRepository;

    public function action(): ResponseInterface
    {
        $page = $this->getArg('page') ?: 1;
        $logs = $this->adminRepository->getAdminLogs($page);
        return $this->render('info/adminlog.html.twig', [
            'logs' => $logs,
            'pagination' => [
                'pages' => $this->adminRepository->getPages(),
                'currentPage' => $page,
                'url' => $this->getUriForRoute($this->getRoute()->getRoute()->getName())
            ]
        ]);
    }

}
