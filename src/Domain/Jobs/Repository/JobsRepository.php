<?php

namespace App\Domain\Jobs\Repository;

use App\Repository\Repository;

class JobsRepository extends Repository
{
    public function getJobsFromDatabase(): array
    {
        return $this->run("SELECT distinct(`job`) as job, sum(minutes) as minutes from role_time WHERE minutes > 100 group by job ORDER BY minutes desc;
        ");
    }

    public function playtimeForJobLastMonth(string $job): int
    {
        return $this->cell("SELECT sum(delta) as minutes FROM role_time_log WHERE job = ? AND YEAR(`datetime`) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
        AND MONTH(`datetime`) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)", $job) + random_int(1, 3) * 10;

    }

    public function deathsByJobLastMonth(string $job): int
    {
        return $this->cell("SELECT count(id) FROM death WHERE job = ? AND YEAR(`tod`) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
        AND MONTH(`tod`) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)", $job) + random_int(1, 10);

    }

    public function bansByJobLastMonth(string $job): int
    {
        return $this->cell("SELECT count(id) FROM ban WHERE role = ? AND YEAR(`bantime`) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
        AND MONTH(`bantime`) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)", $job) + random_int(1, 10);

    }

    public function playtimeForJobAllTime(string $job): int
    {
        return $this->cell("SELECT minutes as minutes FROM role_time WHERE job = ?", $job) + random_int(1, 3) * 10;
        ;
    }

    public function deathsByJobAllTime(string $job): int
    {
        return $this->cell("SELECT count(id) FROM death WHERE job = ?", $job) + random_int(1, 10);

    }

    public function bansByJobAllTime(string $job): int
    {
        return $this->cell("SELECT count(id) FROM ban WHERE role = ?", $job) + random_int(1, 10);
    }

}
