<?php

namespace App\Controller\Jobs;

use App\Controller\Controller;
use App\Domain\Jobs\Repository\JobsRepository;
use App\Domain\Jobs\Data\Jobs;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class JobListController extends Controller
{
    #[Inject]
    private JobsRepository $jobsRepository;

    public function action(): ResponseInterface
    {
        $repoJobs = $this->jobsRepository->getJobsFromDatabase();

        foreach($repoJobs as &$r) {
            $r->enum = Jobs::tryFrom(ucfirst($r->job));
            $r->minutes = $r->minutes + (rand(1, 4) * 10);
        }
        return $this->render('jobs/index.html.twig', [
            'jobs' => $repoJobs
        ]);
    }

}
