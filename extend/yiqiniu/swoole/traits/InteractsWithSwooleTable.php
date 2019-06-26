<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

namespace yiqiniu\swoole\traits;

use Swoole\Table as SwooleTable;
use think\App;
use think\Container;
use think\swoole\Table;

/**
 * Trait InteractsWithSwooleTable
 *
 * @property Container $container
 * @property App       $app
 */
trait InteractsWithSwooleTable
{

    /**
     * @var Table
     */
    protected $currentTable;

    /**
     * Register customized swoole talbes.
     */
    protected function createTables()
    {
        $this->currentTable = new Table();
        $this->registerTables();
    }

    /**
     * Register user-defined swoole tables.
     */
    protected function registerTables()
    {
        $tables = $this->config['table'];

        foreach ($tables as $key => $value) {
            $table   = new SwooleTable($value['size']);
            $columns = $value['columns'] ?? [];
            foreach ($columns as $column) {
                if (isset($column['size'])) {
                    $table->column($column['name'], $column['type'], $column['size']);
                } else {
                    $table->column($column['name'], $column['type']);
                }
            }
            $table->create();

            $this->currentTable->add($key, $table);
        }
    }

    /**
     * Bind swoole table to Laravel app container.
     */
    protected function bindSwooleTable()
    {
        $this->app->bind(Table::class, function () {
            return $this->currentTable;
        });

        $this->app->bind('swoole.table', Table::class);
    }
}
