<?php

namespace App\Controller\Info;

use App\Controller\Controller;
use App\Domain\Admin\Repository\AdminRepository;
use App\Enum\AdminRanks;
use App\Enum\PermissionsFlags;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class AdminRosterController extends Controller
{
    #[Inject]
    private AdminRepository $adminRepository;

    public function action(): ResponseInterface
    {
        $admins = $this->adminRepository->getAdminRoster();
        return $this->render('info/adminroster.html.twig', [
            'admins' => $admins,
            'perms' => PermissionsFlags::getArray()
        ]);
    }

}
