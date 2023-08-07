<?php

namespace App\Domain\Achievement\Repository;

use App\Domain\Achievement\Data\Achievement;
use App\Repository\Repository;

class AchievementRepository extends Repository
{
    public ?string $entityClass = Achievement::class;

    public function getAchievementsForCkey(string $ckey)
    {
        $data = $this->db->run("SELECT
            s.achievement_key,
            s.value,
            s.last_updated,
            a.achievement_version,
            a.achievement_type, 
            a.achievement_name,
            a.achievement_description
            FROM achievements s
            LEFT JOIN achievement_metadata a on a.achievement_key = s.achievement_key
            WHERE s.ckey = ?
            ORDER BY a.achievement_type DESC, a.achievement_name ASC", $ckey);
        $this->setResults($data);
        return $this->getResults();
    }

}
