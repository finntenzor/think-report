<?php
namespace finntenzor\report;

use think\exception\Handle;
use Exception;
use think\Container;

/**
 * 异常处理类
 * @author 董江彬 <dongjiangbin@tiaozhan.com>
 */
class ExceptionHandle extends Handle
{
    public function report(Exception $exception)
    {
        // 忽略一些异常
        if ($this->isIgnoreReport($exception)) {
            return;
        }
        // 渲染错误报告
        $data = $this->getData($exception);
        $content = $this->dataToContent($data);

        // 写入文件
        $filePath = Config::getDirSafe() . Config::timeToFileName(time());
        file_put_contents($filePath, $content);
    }

    public function render(\Exception $e)
    {
        $app = Container::get('app');
        if ($app->bound(ResponseBuilder::class)) {
            $builder = $app->get(ResponseBuilder::class);
        } else {
            $builder = new DefaultResponseBuilder();
        }
        if (Container::get('app')->isDebug()) {
            return $builder->debugResponse($e);
        } else {
            return $builder->deployResponse($e);
        }
    }

    protected function getData(Exception $exception)
    {
        //保留一层
        while (ob_get_level() > 1) {
            ob_end_clean();
        }
        $echo = ob_get_clean();
        // 收集异常数据
        return [
            'name'    => get_class($exception),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'message' => $this->getMessage($exception),
            'trace'   => $exception->getTrace(),
            'code'    => $this->getCode($exception),
            'source'  => $this->getSourceCode($exception),
            'data'    => $this->getExtendData($exception),
            'echo'    => $echo,
            'tables'  => [
                'GET Data'              => $_GET,
                'POST Data'             => $_POST,
                'Files'                 => $_FILES,
                'Cookies'               => $_COOKIE,
                'Session'               => isset($_SESSION) ? $_SESSION : [],
                'Server/Request Data'   => $_SERVER,
                'Environment Variables' => $_ENV,
                'ThinkPHP Constants'    => $this->getConst(),
            ],
        ];
    }

    /**
     * @param array $data 异常信息
     * @return string 渲染后的页面
     */
    protected function dataToContent($data)
    {
        ob_start();
        extract($data);
        include 'report_template.php';
        // 获取并清空缓存
        $content  = ob_get_clean();
        return $content;
    }

    /**
     * 获取常量列表
     * @access private
     * @return array 常量列表
     */
    private static function getConst()
    {
        $const = get_defined_constants(true);

        return isset($const['user']) ? $const['user'] : [];
    }
}
