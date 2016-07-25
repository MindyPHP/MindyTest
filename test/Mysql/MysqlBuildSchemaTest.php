<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 22/06/16
 * Time: 10:12
 */

namespace Mindy\QueryBuilder\Tests;

use Mindy\Query\PDO;
use Mindy\QueryBuilder\Database\Mysql\Adapter;

class MysqlBuildSchemaTest extends BuildSchemaTest
{
    public function getAdapter()
    {
        return new Adapter();
    }

    public function createPDOInstance()
    {
        $config = require(__DIR__ . '/config.php');
        return new PDO($config['dsn'], $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function testRenameColumn()
    {
        $qb = $this->getQueryBuilder();
        $qb->getAdapter()->setDriver($this->createPDOInstance());
        $this->assertSql("ALTER TABLE [[profile]] CHANGE [[description]] [[title]] varchar(200) DEFAULT NULL",
            $qb->renameColumn('profile', 'description', 'title'));
    }

    public function testAlterColumn()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql('ALTER TABLE [[test]] CHANGE [[name]] [[name]] varchar(255)',
            $qb->alterColumn('test', 'name', 'varchar(255)'));
    }

    public function testRenameTable()
    {
        $this->assertSql(
            'RENAME TABLE [[test]] TO [[foo]]',
            $this->getQueryBuilder()->renameTable('test', 'foo')
        );
    }

    public function testDropPrimaryKey()
    {
        $qb = $this->getQueryBuilder();
        $this->assertSql('ALTER TABLE [[test]] DROP PRIMARY KEY', $qb->dropPrimaryKey('test', 'user_id'));
    }

    public function testDropIndex()
    {
        $this->assertSql(
            'DROP INDEX [[name]] ON [[test]]',
            $this->getQueryBuilder()->dropIndex('test', 'name')
        );
    }
}