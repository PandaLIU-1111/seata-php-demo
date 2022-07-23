<?php

namespace App\Controller;

use Hyperf\DbConnection\Annotation\Transactional;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\Seata\Core\Context\RootContext;
use Hyperf\Seata\Rm\DataSource\ConnectionContext;
use Hyperf\Seata\Rm\DataSource\DataSourceManager;
use Hyperf\Seata\Rm\DataSource\MysqlConnection;
use Hyperf\Seata\Rm\DefaultResourceManager;
use Hyperf\Seata\Tm\DefaultTranslationManager;
use Hyperf\Utils\ApplicationContext;
use App\Model\OrderTbl;

/**
 * @AutoController()
 */
class IndexController
{
    public function test()
    {
        $container = ApplicationContext::getContainer();
        $tmManger = $container->get(DefaultTranslationManager::class);
        $connectionContext = $container->get(ConnectionContext::class);
        $dataSourceManager = $container->get(DataSourceManager::class);
        $defaultResourceManager = $container->get(DefaultResourceManager::class);
        $xid = $tmManger->begin('test_application_Id', 'test', 'test', 1000);
        $connectionContext->setXid($xid);
        var_dump('xid:' . $xid);
        Db::beginTransaction();
//
//        $query = OrderTbl::query();
//        $orderConnection = $query->getConnection();
//        $orderConnection->bind($xid);
//        $query->create([
//            'id' => rand(1, 99999999)
//        ]);
//        $res = $defaultResourceManager->branchRegister($orderConnection->getBranchType(),$orderConnection->getResourceId(), '', $xid,'{"a": "a"}', 'test');
//        var_dump($res);
//        ->setXid()

        var_dump('$connectionContext->getXid()::' . $connectionContext->getXid());
        Db::commit();
//        echo 'xid:' . $xid . PHP_EOL;
//        $tmManger->commit($xid);
        return [];
    }
}