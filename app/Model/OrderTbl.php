<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Database\ConnectionInterface;
use Hyperf\Seata\Rm\DataSource\DataSourceProxy;
use Hyperf\Seata\Rm\DataSource\MysqlConnectionProxy;
use Hyperf\Utils\ApplicationContext;

/**
 * @property int $id 
 * @property string $user_id 
 * @property string $commodity_code 
 * @property int $count 
 * @property int $money 
 */
class OrderTbl extends Model
{

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_tbl';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'commodity_code', 'count', 'money'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'count' => 'integer', 'money' => 'integer'];

}