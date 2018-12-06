<?php
namespace finntenzor\report;

use think\exception\Handle;
use Exception;
use think\Container;
use think\facade\Response;

/**
 * 异常处理类
 * @author FinnTenzor <finntenzor@gmail.com>
 */
class ExceptionHandle extends Handle
{
    /**
     * 要忽略的错误类型
     * @var array
     */
    protected static $ignores = [];

    /**
     * 忽略某个错误类型
     * @param string $classPath 类名/路径
     */
    public static function ignore($classPath)
    {
        static::$ignores[] = $classPath;
    }

    /**
     * 接管后的错误报告，将原错误渲染并保存。
     *
     * @access public
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        $this->ignoreReport = array_merge($this->ignoreReport, static::$ignores);
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

    /**
     * 返回给用户的错误报告，由ResponseBuilder决定
     *
     * @access public
     * @param  \Exception $e
     * @return Response
     */
    public function render(\Exception $e)
    {
        // 获取builder
        $app = Container::get('app');
        if ($app->bound(ResponseBuilder::class)) {
            $builder = $app->get(ResponseBuilder::class);
        } else {
            $builder = new DefaultResponseBuilder();
        }
        // 根据debug构建合适的响应
        if (Container::get('app')->isDebug()) {
            $result = $builder->debugResponse($e);
        } else {
            $result = $builder->deployResponse($e);
        }
        // 如果是非响应类，则自动封装为html响应
        if (!$result instanceof \think\Response) {
            $result = Response::create($result, 'html');
        }
        return $result;
    }

    /**
     * 收集错误数据
     * @param Exception $exception
     * @return array
     */
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
     * 将data转换为渲染页面
     * @param array $data 异常信息
     * @return string 渲染后的页面
     */
    protected function dataToContent($data)
    {
        ob_start();
        extract($data);
        include 'templates/report.php';
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
