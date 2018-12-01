<?php
namespace finntenzor\report;

use think\facade\Route;

/**
 * Report
 * 外部访问类
 * @author FinnTenzor <finntenzor@tiaozhan.com>
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
        // 路由组
        return Route::group($groupName, function () {

            // getReportById 路由
            // 显示具体某一个错误报告
            $getReportById = Route::get('/:id', function ($id) {
                // 返回某一条报错
                return ExceptionReporter::getReportById($id);
            });

            // getReportList 路由
            // 显示错误报告列表
            Route::get('/', function () use ($getReportById) {
                // 获取所有报告详情
                $list = ExceptionReporter::getReportList();
                // 计算url，由于某些原因没有使用TP的构建方法
                $rule = '/' . $getReportById->getRule();
                foreach ($list as &$item) {
                    $item['url'] = str_replace('<id>', $item['id'], $rule);
                }
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
