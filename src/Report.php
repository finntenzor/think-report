<?php
namespace finntenzor\report;

use think\facade\Route;

class Report
{
    public static function route($groupName)
    {
        return Route::group($groupName, function () {
            $getReportById = Route::get('/:id', function ($id) {
                // 返回某一条报错
                return ExceptionReporter::getReportById($id);
            });
            Route::get('/', function () use ($getReportById) {
                // 获取所有报告详情
                $list = ExceptionReporter::getReportList();
                // 计算url，由于某些原因没有使用TP的构建方法
                $rule = '/' . $getReportById->getRule();
                foreach ($list as &$item) {
                    $item['url'] = str_replace('<id>', $item['id'], $rule);
                }
                // 按时间排序
                usort($list, function ($a, $b) {
                    // 降序排列，时间戳较大的靠上显示
                    return $b['time'] - $a['time'];
                });
                // 渲染页面
                ob_start();
                include 'templates/list.php';
                // 获取并清空缓存
                $content  = ob_get_clean();
                return $content;
            });
        });
    }
}
