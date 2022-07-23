<?php

namespace Cases;

use Hyperf\Seata\Rm\DataSource\Sql\Struct\Cache\MysqlTableMetaCache;
use Hyperf\Seata\Rm\PDOProxy;
use PHPUnit\Framework\TestCase;

class MysqlTableMetaCacheTest extends TestCase
{
    public function testGetMetaData()
    {
        $dbms='mysql';     //数据库类型
        $host='120.79.29.235'; //数据库主机名
        $dbName='Test';    //使用的数据库
        $user='root';      //数据库连接用户名
        $pass='root';          //对应的密码
        $dsn="$dbms:host=$host;dbname=$dbName";
//        $refClass = new \ReflectionClass(PDOProxy::class);
//        $pdoProxy =  $refClass->newInstanceWithoutConstructor();

        $proxy = new PDOProxy($dsn, $user, $pass, []);

//       $sql = 'SELECT * FROM table where id in (1,2,3,4,5)';
        $sql = 'DELETE FROM `testTables` WHERE id in (1,2,3)';
        $res = $proxy->prepare($sql);
        $res->execute();
        $res = $res->fetchAll();
        dump($res);

//        $pdo = new \PDO($dsn, $user, $pass);
//        $res = new MysqlTableMetaCache();
//        $res = $res->getIndexs($pdo, 'test', null);
//        var_dump($res);
    }
}