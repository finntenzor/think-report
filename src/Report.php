<?php
namespace finntenzor\report;

use think\facade\Route;

/**
 * Report
 * 外部访问类
 * @author FinnTenzor <finntenzor@gmail.com>
 */
class Report
{
    /**
     * 创建错误报告路由组
     * @param string $groupName 组名
     * @return \think\route\RuleGroup 生成的路由组
     */
    public static function route($groupName)
    {
        return Route::group($groupName, function () {
            Route::get('/:id', '\finntenzor\report\ReportController@getReportById');
            Route::get('/', '\finntenzor\report\ReportController@getReportList');
        });
    }
}
