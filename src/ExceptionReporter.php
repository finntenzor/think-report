<?php
namespace finntenzor\report;

/**
 * ExceptionReporter
 * 错误报告类
 * 通过此类来显示某一份错误报告或者返回错误报告列表
 * @author 董江彬 <finntenzor@gmail.com>
 */
class ExceptionReporter
{
    /**
     * 获取错误全部错误报告列表
     * @return array 详情
     */
    public static function getReportList()
    {
        $dir = Config::getDirSafe();
        // 打开文件夹
        $handler = opendir($dir);
        // 读取所有文件
        $detailList = [];
        while (($fileName = readdir($handler)) !== false) {
            // 过滤Linux下的“.”和“..”
            if ($fileName == '.' || $fileName == '..') {
                continue;
            }
            $detailList[] = Config::fileNameToDetails($fileName);
        }
        // 按时间排序
        usort($detailList, function ($a, $b) {
            // 降序排列，时间戳较大的靠上显示
            return $b['time'] - $a['time'];
        });
        // 关闭目录
        closedir($handler);
        // 返回详情
        return $detailList;
    }

    /**
     * 通过id获取某个错误报告
     * @param int $id 错误报告id（时间戳+随机数标识码）
     * @return string html字符串
     */
    public static function getReportById($id)
    {
        // 构建路径
        $path = Config::getDirSafe() . $id . ".html";
        // 打开文件，不存在则返回无结果
        if (file_exists($path)) {
            $result = file_get_contents($path);
        } else {
            $result = 'No such report';
        }
        return $result;
    }
}
