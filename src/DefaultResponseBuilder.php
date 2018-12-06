<?php
namespace finntenzor\report;

/**
 * DefaultResponseBuilder
 * 用于控制默认返回给用户的结果
 * @author FinnTenzor <finntenzor@gmail.com>
 */
class DefaultResponseBuilder implements ResponseBuilder
{
    /**
     * debug模式下返回响应
     * @param \Exception $e 异常
     * @return mixed 响应
     */
    public function debugResponse($e)
    {
        return json([
            'ret' => 500,
            'msg' => $e->getMessage()
        ]);
    }

    /**
     * 非debug模式下返回响应
     * @return mixed 响应
     */
    public function deployResponse()
    {
        return json([
            'ret' => 500,
            'msg' => 'Internal Server Error'
        ]);
    }
}
