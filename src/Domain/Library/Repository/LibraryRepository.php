<?php

namespace App\Domain\Library\Repository;

use App\Domain\Library\Data\Book;
use App\Domain\User\Data\User;
use App\Repository\Repository;
use Exception;

class LibraryRepository extends Repository
{
    public ?string $entityClass = Book::class;

    private array $columns = [
        'b.id',
        'b.author',
        'b.title',
        'b.content',
        'b.category',
        'b.ckey',
        'b.datetime',
        'b.deleted',
        'b.round_id_created AS round',
        'r.rank',
    ];

    private array $joins = [
        'LEFT JOIN `admin` AS r ON b.ckey = r.ckey',
    ];

    private array $where = [
        'b.deleted IS NULL OR 0'
    ];

    private array $orderBy = [
        'b.datetime DESC'
    ];

    private string $table = 'library b';

    public function getLibrary(int $page = 1, int $per_page = 24, $term = false): array
    {
        if ($term) {
            $statement = \ParagonIE\EasyDB\EasyStatement::open()->andWith('b.content like ?', '%' . $this->db->escapeLikeValue($term) . '%');
            $this->where = [$statement->sql(), ...$this->where];
        }

        $pagesQuery = $this->buildQuery($this->table, ['count(b.id)'], [], $this->where, [], false);
        if($term) {
            $this->setPages((int) ceil($this->cell($pagesQuery, $statement->values()[0]) / $per_page));
        } else {
            $this->setPages((int) ceil($this->cell($pagesQuery) / $per_page));
        }
        $query = $this->buildQuery($this->table, $this->columns, $this->joins, $this->where, $this->orderBy, '?,?');

        if($term) {
            $this->setResults($this->run(
                $query,
                $statement->values()[0],
                ($page * $per_page) - $per_page,
                $per_page
            ));
        } else {
            $this->setResults($this->run(
                $query,
                ($page * $per_page) - $per_page,
                $per_page
            ));
        }
        return $this->getResults();
    }

    public function getBook(int $book, bool $allowDeleted = false): Book
    {
        $this->where = ['b.id = ?', ...$this->where];
        if($allowDeleted) {
            $this->where =  ['b.id = ?'];
        }
        $query = $this->buildQuery($this->table, $this->columns, $this->joins, $this->where, false, false);
        $this->setResult($this->actualRow($query, [$book]));
        return $this->getResult();
    }

    public function toggleBookDeletion(Book $book, User $user): bool
    {
        try {
            $this->db->update('library', [
                'deleted' => $book->isDeleted()
            ], [
                'id' => $book->id
            ]);
            $this->db->insert('external_activity', [
                'ckey' => $user->getCkey(),
                'ip' => ip2long($_SERVER['REMOTE_ADDR']),
                'action' => (!$book->isDeleted() ? 'F451' : 'F452'),
                'text' => (!$book->isDeleted() ? "Deleted book $book->id" : "Undeleted book $book->id")
            ]);
        } catch (Exception $e) {
            die($e->getMessage());
        }
        return true;
    }

}
