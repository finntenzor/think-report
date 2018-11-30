<?php
namespace finntenzor\report;

interface ResponseBuilder
{
    /**
     * @param \Exception $e 异常
     * @return mixed 响应
     */
    public function debugResponse($e);

    /**
     * @return mixed 响应
     */
    public function deployResponse();
}
