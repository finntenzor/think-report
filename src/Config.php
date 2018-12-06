<?php
namespace finntenzor\report;

use think\facade\Env;

// TODO 将此类改为可以配置项

/**
 * Config
 * 异常报告的耦合配置
 * @author FinnTenzor <finntenzor@gmail.com>
 */
class Config
{
    /**
     * 获取reports目录
     * @return string reports目录
     */
    public static function getDir()
    {
        return Env::get('runtime_path') . 'reports/';
    }

    /**
     * 获取reports目录，如果不存在则创建
     * @return string reports目录
     */
    public static function getDirSafe()
    {
        $dir = static::getDir();
        !is_dir($dir) && mkdir($dir, 0755, true);
        return $dir;
    }

    /**
     * 时间戳转文件名
     * @param int $time 时间戳
     * @return string 文件名
     */
    public static function timeToFileName($time)
    {
        $fileName = strval($time) . rand(10000, 99999);
        return $fileName . '.html';
    }

    /**
     * 从文件名获取详情
     * @param string $fileName 文件名
     * @return array 相关参数
     */
    public static function fileNameToDetails($fileName)
    {
        $indexPoint = strpos($fileName, '.');
        $id = substr($fileName, 0, $indexPoint);
        $time = substr($fileName, 0, $indexPoint - 5);
        $date = date('Y-m-d H:i:s', $time);
        return [
            'id' => $id,
            'time' => $time,
            'date' => $date
        ];
    }
}
