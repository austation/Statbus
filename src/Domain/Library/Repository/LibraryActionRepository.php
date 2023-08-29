<?php

namespace App\Domain\Library\Repository;

use App\Domain\Library\Data\LibraryAction;
use App\Repository\Repository;

class LibraryActionRepository extends Repository
{
    public ?string $entityClass = LibraryAction::class;

    private array $columns = [
        'r.id',
        'r.book',
        'r.reason',
        'r.ckey',
        'r.datetime',
        'r.action',
        'a.rank',
    ];

    private array $joins = [
        '`admin` AS a ON r.ckey = a.ckey',
    ];

    private array $where = [
        'r.book = ?'
    ];

    private array $orderBy = [
        'r.datetime DESC'
    ];

    private string $table = 'library_action r';

    public function getActionsForBook(int $book): array
    {
        $query = $this->buildQuery($this->table, $this->columns, $this->joins, $this->where, $this->orderBy, false);
        $this->setResults($this->run(
            $query,
            $book
        ));
        return $this->getResults();
    }

}
