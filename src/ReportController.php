<?php

namespace finntenzor\report;

use think\facade\Url;

/**
 * ResponseBuilder
 * 该接口包含两个方法，分别用于debug模式和非debug模式时如何返回给用户响应。
 * @author FinnTenzor <finntenzor@gmail.com>
 */
class ReportController
{
    /**
     * 显示具体某一个错误报告
     * @param int $id 错误报告id
     */
    public function getReportById($id)
    {
        return ExceptionReporter::getReportById($id);
    }

    /**
     * 显示错误报告列表
     */
    public function getReportList()
    {
        // 获取所有错误报告
        $list = ExceptionReporter::getReportList();
        // 拼接url
        foreach ($list as &$item) {
            $item['url'] = Url::build('\finntenzor\report\ReportController@getReportById', [
                'id' => $item['id']
            ]);
        }
        // 渲染页面
        ob_start();
        include 'templates/list.php';
        // 获取并清空缓存
        $content  = ob_get_clean();
        return $content;
    }
}
