<?php

namespace Mindy\QueryBuilder;

use function Mindy\app;
use Mindy\Helper\Traits\Accessors;
use Mindy\Helper\Traits\Configurator;

class Migration
{
    use Accessors, Configurator;

    /**
     * @var string
     */
    protected $db = 'default';

    /**
     * This method contains the logic to be executed when applying this migration.
     * Child classes may overwrite this method to provide actual migration logic.
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function up()
    {
        $transaction = $this->getDb()->beginTransaction();
        try {
            if ($this->safeUp() === false) {
                $transaction->rollback();
                return false;
            }
            $transaction->commit();
        } catch (\Exception $e) {
            echo "Exception: " . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")\n";
            echo $e->getTraceAsString() . "\n";
            $transaction->rollback();
            return false;
        }
        return null;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * The default implementation throws an exception indicating the migration cannot be removed.
     * Child classes may override this method if the corresponding migrations can be removed.
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function down()
    {
        $transaction = $this->getDb()->beginTransaction();
        try {
            if ($this->safeDown() === false) {
                $transaction->rollback();
                return false;
            }
            $transaction->commit();
        } catch (\Exception $e) {
            echo "Exception: " . $e->getMessage() . ' (' . $e->getFile() . ':' . $e->getLine() . ")\n";
            echo $e->getTraceAsString() . "\n";
            $transaction->rollback();
            return false;
        }
        return null;
    }

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
    }

    /**
     * Executes a SQL statement.
     * This method executes the specified SQL statement using [[db]].
     * @param string $sql the SQL statement to be executed
     * @param array $params input parameters (name => value) for the SQL execution.
     * See [[Command::execute()]] for more details.
     */
    public function execute($sql, $params = [])
    {
        echo "    > execute SQL: $sql ...";
        $time = microtime(true);
        $this->getDb()->createCommand($sql, $params)->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Creates and executes an INSERT SQL statement.
     * The method will properly escape the column names, and bind the values to be inserted.
     * @param string $table the table that new rows will be inserted into.
     * @param array $columns the column data (name => value) to be inserted into the table.
     */
    public function insert($table, $columns, $rows)
    {
        echo "    > insert into $table ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->insert($table, $columns, $rows))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Creates and executes an UPDATE SQL statement.
     * The method will properly escape the column names and bind the values to be updated.
     * @param string $table the table to be updated.
     * @param array $columns the column data (name => value) to be updated.
     * @param array|string $condition the conditions that will be put in the WHERE part. Please
     * refer to [[Query::where()]] on how to specify conditions.
     * @param array $params the parameters to be bound to the query.
     */
    public function update($table, $columns, $condition = '', $params = [])
    {
        echo "    > update $table ...";
        $time = microtime(true);
        $sql = $this->getQueryBuilder()->setTypeUpdate()->where($condition)->update($table, $columns);
        $this->getDb()->createCommand($sql, $params)->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Creates and executes a DELETE SQL statement.
     * @param string $table the table where the data will be deleted from.
     * @param array|string $condition the conditions that will be put in the WHERE part. Please
     * refer to [[Query::where()]] on how to specify conditions.
     * @param array $params the parameters to be bound to the query.
     */
    public function delete($table, $condition = '', $params = [])
    {
        echo "    > delete from $table ...";
        $time = microtime(true);
        $sql = $this->getQueryBuilder()->setTypeDelete()->from($table)->where($condition)->toSQL();
        $this->getDb()->createCommand($sql,  $params)->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for creating a new DB table.
     *
     * The columns in the new  table should be specified as name-definition pairs (e.g. 'name' => 'string'),
     * where name stands for a column name which will be properly quoted by the method, and definition
     * stands for the column type which can contain an abstract DB type.
     *
     * The [[QueryBuilder::getColumnType()]] method will be invoked to convert any abstract type into a physical one.
     *
     * If a column is specified with definition only (e.g. 'PRIMARY KEY (name, type)'), it will be directly
     * put into the generated SQL.
     *
     * @param string $table the name of the table to be created. The name will be properly quoted by the method.
     * @param array $columns the columns (name => definition) in the new table.
     * @param string $options additional SQL fragment that will be appended to the generated SQL.
     * @param bool $ifNotExists additional SQL fragment IF NOT EXISTS that will be appended after CREATE TABLE.
     */
    public function createTable($table, $columns, $options = null, $ifNotExists = false)
    {
        echo "    > create table $table ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->createTable($table, $columns, $options, $ifNotExists))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for renaming a DB table.
     * @param string $table the table to be renamed. The name will be properly quoted by the method.
     * @param string $newName the new table name. The name will be properly quoted by the method.
     */
    public function renameTable($table, $newName)
    {
        echo "    > rename table $table to $newName ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->renameTable($table, $newName))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for dropping a DB table.
     * @param string $table the table to be dropped. The name will be properly quoted by the method.
     */
    public function dropTable($table)
    {
        echo "    > drop table $table ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->dropTable($table))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for truncating a DB table.
     * @param string $table the table to be truncated. The name will be properly quoted by the method.
     */
    public function truncateTable($table)
    {
        echo "    > truncate table $table ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->truncateTable($table))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for adding a new DB column.
     * @param string $table the table that the new column will be added to. The table name will be properly quoted by the method.
     * @param string $column the name of the new column. The name will be properly quoted by the method.
     * @param string $type the column type. The [[QueryBuilder::getColumnType()]] method will be invoked to convert abstract column type (if any)
     * into the physical one. Anything that is not recognized as abstract type will be kept in the generated SQL.
     * For example, 'string' will be turned into 'varchar(255)', while 'string not null' will become 'varchar(255) not null'.
     */
    public function addColumn($table, $column, $type)
    {
        echo "    > add column $column $type to table $table ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->addColumn($table, $column, $type))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for dropping a DB column.
     * @param string $table the table whose column is to be dropped. The name will be properly quoted by the method.
     * @param string $column the name of the column to be dropped. The name will be properly quoted by the method.
     */
    public function dropColumn($table, $column)
    {
        echo "    > drop column $column from table $table ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->dropColumn($table, $column))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for renaming a column.
     * @param string $table the table whose column is to be renamed. The name will be properly quoted by the method.
     * @param string $name the old name of the column. The name will be properly quoted by the method.
     * @param string $newName the new name of the column. The name will be properly quoted by the method.
     */
    public function renameColumn($table, $name, $newName)
    {
        echo "    > rename column $name in table $table to $newName ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->renameColumn($table, $name, $newName))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for changing the definition of a column.
     * @param string $table the table whose column is to be changed. The table name will be properly quoted by the method.
     * @param string $column the name of the column to be changed. The name will be properly quoted by the method.
     * @param string $type the new column type. The [[getColumnType()]] method will be invoked to convert abstract column type (if any)
     * into the physical one. Anything that is not recognized as abstract type will be kept in the generated SQL.
     * For example, 'string' will be turned into 'varchar(255)', while 'string not null' will become 'varchar(255) not null'.
     */
    public function alterColumn($table, $column, $type)
    {
        echo "    > alter column $column in table $table to $type ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->alterColumn($table, $column, $type))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for creating a primary key.
     * The method will properly quote the table and column names.
     * @param string $name the name of the primary key constraint.
     * @param string $table the table that the primary key constraint will be added to.
     * @param string|array $columns comma separated string or array of columns that the primary key will consist of.
     */
    public function addPrimaryKey($name, $table, $columns)
    {
        echo "    > add primary key $name on $table (" . (is_array($columns) ? implode(',', $columns) : $columns) . ") ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->addPrimaryKey($name, $table, $columns))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for dropping a primary key.
     * @param string $name the name of the primary key constraint to be removed.
     * @param string $table the table that the primary key constraint will be removed from.
     * @return Command the command object itself
     */
    public function dropPrimaryKey($name, $table)
    {
        echo "    > drop primary key $name ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->dropPrimaryKey($name, $table))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds a SQL statement for adding a foreign key constraint to an existing table.
     * The method will properly quote the table and column names.
     * @param string $name the name of the foreign key constraint.
     * @param string $table the table that the foreign key constraint will be added to.
     * @param string $columns the name of the column to that the constraint will be added on. If there are multiple columns, separate them with commas.
     * @param string $refTable the table that the foreign key references to.
     * @param string $refColumns the name of the column that the foreign key references to. If there are multiple columns, separate them with commas.
     * @param string $delete the ON DELETE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
     * @param string $update the ON UPDATE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
     */
    public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        echo "    > add foreign key $name: $table ($columns) references $refTable ($refColumns) ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds a SQL statement for dropping a foreign key constraint.
     * @param string $name the name of the foreign key constraint to be dropped. The name will be properly quoted by the method.
     * @param string $table the table whose foreign is to be dropped. The name will be properly quoted by the method.
     */
    public function dropForeignKey($name, $table)
    {
        echo "    > drop foreign key $name from table $table ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->dropForeignKey($name, $table))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for creating a new index.
     * @param string $name the name of the index. The name will be properly quoted by the method.
     * @param string $table the table that the new index will be created for. The table name will be properly quoted by the method.
     * @param string $column the column(s) that should be included in the index. If there are multiple columns, please separate them
     * by commas. The column names will be properly quoted by the method.
     * @param boolean $unique whether to add UNIQUE constraint on the created index.
     */
    public function createIndex($name, $table, $column, $unique = false)
    {
        echo "    > create" . ($unique ? ' unique' : '') . " index $name on $table ($column) ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->createIndex($name, $table, $column, $unique))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * Builds and executes a SQL statement for dropping an index.
     * @param string $name the name of the index to be dropped. The name will be properly quoted by the method.
     * @param string $table the table whose index is to be dropped. The name will be properly quoted by the method.
     */
    public function dropIndex($name, $table)
    {
        echo "    > drop index $name ...";
        $time = microtime(true);
        $this->getDb()->createCommand($this->getQueryBuilder()->dropIndex($name, $table))->execute();
        echo " done (time: " . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    /**
     * @return \Mindy\QueryBuilder\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->getDb()->getQueryBuilder();
    }

    /**
     * @return \Mindy\Query\Connection
     */
    protected function getDb()
    {
        return app()->db->getDb($this->db);
    }
}