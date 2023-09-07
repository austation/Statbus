<?php

namespace App\Controller\Jobs;

use App\Controller\Controller;
use App\Domain\Jobs\Repository\JobsRepository;
use App\Domain\Jobs\Data\Jobs;
use Psr\Http\Message\ResponseInterface;
use DI\Attribute\Inject;

class JobViewController extends Controller
{
    #[Inject]
    private JobsRepository $jobsRepository;

    public function action(): ResponseInterface
    {

        $job = urldecode($this->getArg('job'));
        // $job = constant("App\Domain\Jobs\Data\Jobs::".$job);
        $job = Jobs::tryFrom($job);
        return $this->render('jobs/single.html.twig', [
            'job' => $job,
            'minutes' => $this->jobsRepository->playtimeForJobLastMonth($job->value),
            'allTime' => $this->jobsRepository->playtimeForJobAllTime($job->value),
            'deathsMonth' => $this->jobsRepository->deathsByJobLastMonth($job->value),
            'deathsAll' => $this->jobsRepository->deathsByJobAllTime($job->value),
            'bansMonth' => $this->jobsRepository->bansByJobLastMonth($job->value),
            'bansAll' => $this->jobsRepository->bansByJobAllTime($job->value)
        ]);
    }

}
