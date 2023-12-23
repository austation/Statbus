<?php

namespace App\Repository;

use App\Factory\LoggerFactory;
use App\Service\HTMLSanitizerService;
use App\Service\ServerInformationService;
use Cake\Database\Connection;
use Cake\Database\Query;
use DateTime;
use ParagonIE\EasyDB\EasyDB;
use RuntimeException;
use DI\Attribute\Inject;
use PDO;

/**
 * Factory.
 */
class Repository
{
    public ?string $entityClass = null;

    public array $timestampedColumns = [
        'expiration',
        'bantime',
        'unbanned_datetime',
        'initialize_datetime',
        'start_datetime',
        'shutdown_datetime',
        'end_datetime',
        'timestamp',
        'firstseen',
        'lastseen',
        'accountJoined',
        'accountjoindate',
        'last_updated',
        'expire_timestamp',
        'datetime',
        'tod'
    ];

    public array $serverPortColumns = [
        'port',
        'server_port',
    ];

    public array $stripHTMLColumns = [
        'message',
        'edits',
        'text',
    ];

    private array $queries = [];

    public $results = null;
    public $result = null;
    public ?int $pages = null;

    private ?array $servers = null;

    #[Inject]
    private HTMLSanitizerService $html;

    protected $logger;

    public function __construct(
        protected Connection $connection,
        protected EasyDB $db,
        protected ?EasyDB $alt_db = null
    ) {
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
                if(0 < $v) {
                    if(is_array($data)) {
                        $data['server'] = ServerInformationService::getServerFromPort($v, $this->servers);
                    } else {
                        $data->server = ServerInformationService::getServerFromPort($v, $this->servers);
                    }
                } else {
                    //Failsafe to catch data (notes) that are old enough that
                    //the server port wasn't saved
                    if(is_array($data)) {
                        $data['server'] = ServerInformationService::getServerFromName($data['server'], $this->servers);
                    } else {
                        $data->server = ServerInformationService::getServerFromName($data->server, $this->servers);
                    }
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

    protected function setResult(array|object $result): self
    {
        $result = $this->parseTimestamps($result);
        $result = $this->mapServer($result);
        $result = $this->stripHTML($result);
        if($this->entityClass && class_exists($this->entityClass)) {
            $result = new $this->entityClass(...array_values((array) $result));
        }
        $this->result = $result;
        return $this;
    }

    protected function setResults(array|object $results, bool $skipParse = false): self
    {
        if(!$skipParse) {
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
            } else {
                $results = $this->parseTimestamps($results);
                $results = $this->mapServer($results);
                $results = $this->stripHTML($results);
                if($this->entityClass && class_exists($this->entityClass)) {
                    $results = new $this->entityClass(...array_values((array) $results));
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

    public function getResult(): array|object|null
    {
        return $this->result;
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

    public function countResults(int $per_page = 60): int
    {
        return $this->pages * $per_page;
    }

    public function run($query, ...$params)
    {
        $this->queries[] = $query;
        return $this->db->run($query, ...$params);
    }

    public function cell(
        string $statement,
        float|object|bool|int|string|null ...$params
    ): float|bool|int|string|null {
        $this->queries[] = $statement;
        return $this->db->cell($statement, ...$params);
    }

    /**
     * actualRow
     *
     * Reimplements EasyDB's row function to force the fetch to be done as an
     * array instead of an object because I can't override that in the row()
     * function
     *
     * @param string $statement
     * @param array $parameters
     * @return void
     */
    public function actualRow(string $statement, array $parameters = []): array
    {
        $data = $this->db->safeQuery($statement, $parameters, PDO::FETCH_ASSOC);
        if (is_array($data)) {
            $first = array_shift($data);
            return $first;
        }
        return [];
    }

    public function buildQuery(string $table, array $columns, bool|array $joins = false, array $where, bool|array $order = [], string $limit = '0, 100', ?array $groupBy = null): string
    {
        $columns = implode(",\n", $columns);
        if($joins) {
            $joinLine = '';
            foreach ($joins as $j) {
                $j = str_replace('LEFT JOIN ', '', $j);
                $joinLine .= "LEFT JOIN $j\n";
            }
            $joins = $joinLine;
        } else {
            $joins = '';
        }
        $where = implode("\n AND ", $where);
        if($order) {
            $order = implode("\n AND ", $order);
        }
        $group = null;
        if($groupBy){
            $group = implode(", ", $groupBy);
        }
        $query = sprintf(
            "SELECT %s FROM %s %s \n WHERE %s
        %s %s %s %s",
            $columns,
            $table,
            $joins,
            $where,
            $group ? "GROUP BY $group" : null,
            $order ? "ORDER BY $order" : null,
            $limit ? 'LIMIT' : null,
            $limit ?: null
        );
        // var_dump($query);
        return $query;
    }

}
