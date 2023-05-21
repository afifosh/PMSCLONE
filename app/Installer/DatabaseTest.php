<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Installer;

use Illuminate\Database\Connection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDOException;

class DatabaseTest
{
    /**
     * @var string|null
     */
    protected $lastError;

    /**
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * Test table name
     */
    protected string $testTable = 'test_table';

    /**
     * Initialize new DatabaseTest instance
     */
    public function __construct(protected Connection $connection)
    {
        $this->schema = Schema::connection($this->connection->getName());
    }

    /**
     * Test DROP privilege
     */
    public function testDropTable(): void
    {
        try {
            // Even if there is no table, will fail if the DROP privilege is not granted
            $this->dropTable();
        } catch (QueryException $e) {
            $this->lastError = $e->getMessage();
        }
    }

    /**
     * Test CREATE privilege
     */
    public function testCreateTable(): void
    {
        $this->performTest(function () {
            $this->createTable();
        });
    }

    /**
     * Test INSERT privilege
     */
    public function testInsert(): void
    {
        $this->performTest(function () {
            $this->createTable();
            $this->insertRow();
        });
    }

    /**
     * Test SELECT privilege
     */
    public function testSelect(): void
    {
        $this->performTest(function () {
            $this->createTable();
            DB::usingConnection($this->connection->getName(), function () {
                DB::select("SELECT * FROM {$this->testTable}");
            });
        });
    }

    /**
     * Test UPDATE privilege
     */
    public function testUpdate(): void
    {
        $this->performTest(function () {
            $this->createTable();
            $this->insertRow();
            DB::usingConnection($this->connection->getName(), function () {
                DB::table($this->testTable)->update(['test_column' => 'Concord']);
            });
        });
    }

    /**
     * Test DELETE privilege
     */
    public function testDelete(): void
    {
        $this->performTest(function () {
            $this->createTable();
            $this->insertRow();
            DB::usingConnection($this->connection->getName(), function () {
                DB::table($this->testTable)->delete();
            });
        });
    }

    /**
     * Test ALTER privilege
     */
    public function testAlter(): void
    {
        $this->performTest(function () {
            $this->createTable(function ($table) {
                $table->primary('id');
            });
        });
    }

    /**
     * Test INDEX privilege
     */
    public function testIndex(): void
    {
        $this->performTest(function () {
            $this->createTable();
            $this->insertRow();
            $this->connection->getPdo()->exec(
                "CREATE INDEX test_column_index ON {$this->testTable} (test_column(10))"
            );
        });
    }

    /**
     * Test REFERENCES privilege
     */
    public function testReferences(): void
    {
        try {
            $this->createTable(function ($table) {
                $table->primary('id');
            }, 'test_users');

            $this->createTable(function ($table) {
                $table->primary('id');
                $table->unsignedBigInteger('test_user_id');
                $table->foreign('test_user_id')
                    ->references('id')
                    ->on('test_users');
            });
        } catch (QueryException $e) {
            $this->lastError = $e->getMessage();
        } finally {
            $this->dropTable($this->testTable);
            $this->dropTable('test_users');
        }
    }

    /**
     * Get the last test error
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Drop table
     */
    protected function dropTable(?string $tableName = null): void
    {
        $this->schema->dropIfExists($tableName ?: $this->testTable);
    }

    /**
     * Perform test
     *
     * @param  \Closure  $callback
     */
    protected function performTest($callback): void
    {
        try {
            $callback();
        } catch (QueryException|PDOException $e) {
            $this->lastError = $e->getMessage();
        } finally {
            $this->dropTable();
        }
    }

    /**
     * Create test table
     *
     * @param  \Closure|null  $callback
     * @param  string|null  $tableName
     */
    protected function createTable($callback = null, $tableName = null): void
    {
        $this->schema->create($tableName ?: $this->testTable, function ($table) use ($callback) {
            $table->unsignedBigInteger('id');
            $table->string('test_column');

            if ($callback) {
                $callback($table);
            }
        });
    }

    /**
     * Insert test row in the test table
     */
    protected function insertRow(): void
    {
        DB::usingConnection($this->connection->getName(), function () {
            DB::insert(
                'insert into '.$this->testTable.' (id, test_column) values (?, ?)',
                [1, 'Concord']
            );
        });
    }
}
