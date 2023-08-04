<?php

namespace App\Repository;

use App\Service\HTMLSanitizerService;
use App\Service\ServerInformationService;
use Cake\Database\Connection;
use Cake\Database\Query;
use DateTime;
use ParagonIE\EasyDB\EasyDB;
use RuntimeException;
use DI\Attribute\Inject;

/**
 * Factory.
 */
class Repository
{
    protected Connection $connection;

    protected $db = null;

    public ?string $entityClass = null;

    private $purifier;

    public array $timestampedColumns = [
        'expiration',
        'bantime',
        'unbanned_datetime',
        'initialize_datetime',
        'start_datetime',
        'shutdown_datetime',
        'end_datetime',
        'timestamp'
    ];

    public array $serverPortColumns = [
        'port',
        'server_port'
    ];

    public array $stripHTMLColumns = [
        'message'
    ];

    public $results = null;
    public ?int $pages = null;

    private ?array $servers = null;

    #[Inject]
    private HTMLSanitizerService $html;

    public function __construct(Connection $connection, EasyDB $db)
    {
        $this->connection = $connection;
        $this->db = $db;

    }

    /**
     * Create a new 'select' query for the given table.
     *
     * @param string $table The table name
     *
     * @throws RuntimeException
     *
     * @return Query A new select query
     */
    public function newSelect(string $table): Query
    {
        return $this->newQuery()->from($table);
    }

    /**
     * Create a new query.
     *
     * @return Query The query
     */
    public function newQuery(): Query
    {
        return $this->connection->newQuery();
    }

    /**
     * Create an 'update' statement for the given table.
     *
     * @param string $table The table to update rows from
     * @param array $data The values to be updated
     *
     * @return Query The new update query
     */
    public function newUpdate(string $table, array $data): Query
    {
        return $this->newQuery()->update($table)->set($data);
    }

    /**
     * Create an 'update' statement for the given table.
     *
     * @param string $table The table to update rows from
     * @param array $data The values to be updated
     *
     * @return Query The new insert query
     */
    public function newInsert(string $table, array $data): Query
    {
        return $this->newQuery()->insert(array_keys($data))
            ->into($table)
            ->values($data);
    }

    /**
     * Create a 'delete' query for the given table.
     *
     * @param string $table The table to delete from
     *
     * @return Query A new delete query
     */
    public function newDelete(string $table): Query
    {
        return $this->newQuery()->delete($table);
    }

    protected function parseTimestamps(object|array $data): object|array
    {
        foreach($data as $k => &$v) {
            if(in_array($k, $this->timestampedColumns)) {
                if(is_null($v)) {
                    $v = null;
                } else {
                    $v = new DateTime($v);
                }
            }
        }
        return $data;
    }

    protected function mapServer(object|array $data): object|array
    {
        if(!$this->servers) {
            $this->servers = ServerInformationService::getServerInfo();
        }
        foreach($data as $k => &$v) {
            if(in_array($k, $this->serverPortColumns)) {
                if(is_null($v)) {
                    $v = null;
                } else {
                    $data->server = ServerInformationService::getServerFromPort($v, $this->servers);
                }
            }
        }
        return $data;
    }

    protected function stripHTML(object|array $data): object|array
    {
        foreach($data as $k => &$v) {
            if(in_array($k, $this->stripHTMLColumns)) {
                if(is_null($v)) {
                    $v = null;
                } else {
                    $v = $this->html->sanitizeString($v);
                }
            }
        }
        return $data;
    }

    protected function setResults($results): self
    {
        if(is_array($results)) {
            foreach($results as &$r) {
                //These should be attributes
                $r = $this->parseTimestamps($r);
                $r = $this->mapServer($r);
                $r = $this->stripHTML($r);
                if($this->entityClass && class_exists($this->entityClass)) {
                    $r = new $this->entityClass(...array_values((array) $r));
                }
            }
        }

        $this->results = $results;
        return $this;
    }

    public function getResults(): array|object|null
    {
        return $this->results;
    }
    public function setPages(int $pages): self
    {
        $this->pages = $pages;
        return $this;
    }
    public function getPages(): int
    {
        return $this->pages;
    }
}
