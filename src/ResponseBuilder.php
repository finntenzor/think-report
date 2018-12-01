<?php
namespace finntenzor\report;

/**
 * ResponseBuilder
 * 该接口包含两个方法，分别用于debug模式和非debug模式时如何返回给用户响应。
 * @author FinnTenzor <finntenzor@tiaozhan.com>
 */
interface ResponseBuilder
{
    /**
     * debug模式下返回响应
     * @param \Exception $e 异常
     * @return mixed 响应
     */
    public function debugResponse($e);

    /**
     * 非debug模式下返回响应
     * @return mixed 响应
     */
    public function deployResponse();
}
