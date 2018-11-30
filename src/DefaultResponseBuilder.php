<?php
namespace finntenzor\report;

class DefaultResponseBuilder implements ResponseBuilder
{
    /**
     * @param Exception $e 异常
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
