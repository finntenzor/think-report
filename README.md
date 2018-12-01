# think-report
A simple reporter for ThinkPHP v5.1
基于ThinkPHP v5.1的简易错误报告工具

你是否遇到上线的项目需要debug，但是又因为打开debug配置等同于让自己的应用“裸奔”而苦恼？那么你一定需要试试这个。

## 如何使用

### 使用异常处理
在你的ThinkPHP项目中，找到config/app.php。在大概最后一行的位置找到“exception_handle”配置，将它修改为“\\\\finntenzor\\\\report\\\\ExceptionHandle”，如：
``` php
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '\\finntenzor\\report\\ExceptionHandle',
```

### 修改路由
在你的ThinkPHP项目中，找到route/route.php。在你需要的位置添加如下代码：
``` php
\finntenzor\report\Report::route('reports');
```
它某种意义上等价于自动编写一个Group以及对应的两个路由：
``` php
Route::group('reports', function () {
    Route::get('/:id', 'finntenzor/reports/getReportById');
    Route::get('/', 'finntenzor/reports/getReportList');
});
```
因此你可以很轻松地将它和其他路由混合在一起：
``` php
use finntenzor\report\Report;

Route::group('app', function () {
    Route::group('api', function () {
        // 一些其他路由
    });
    // 将app/reports注册为错误报告路由
    Report::route('reports');
});
```
如上代码所示，你可以访问`/app/reports`来查看所有的错误报告，其中的各项报告链接会自动处理。

并且，它返回了ThinkPHP的返回值，你还可以将它跟中间件等其他功能结合在一起。
``` php
use finntenzor\report\Report;

Route::group('app', function () {
    Route::group('api', function () {
        // 一些其他路由
    });
    // 将app/reports注册为错误报告路由
    Report::route('reports')->middleware('MustAdmin');
});
```

### 取代默认响应格式
错误报告工具启动后，默认会保存所有的错误报告到项目目录/runtime/reports下，但是仅进行保存而不会将错误返回给用户显示，给用户/前端的将会是一段默认的json，格式如下：

+ 项目关闭debug模式时
  ``` json
  {
    "ret": 500,
    "msg": "Internal Server Error"
  }
  ```

+ 项目打开debug模式时
  ``` json
  {
    "ret": 500,
    "msg": "错误Message"
  }
  ```

如果你需要修改为其他格式，你可以利用ThinkPHP的容器绑定来修改格式：

  1. 首先你需要创建一个类实现ResponseBuilder，例如：
    ``` php
    <?php
    namespace app\index\common;

    use finntenzor\report\ResponseBuilder;

    class ExceptionResponseBuilder implements ResponseBuilder
    {

        /**
        * @param \Exception $e 异常
        * @return mixed 响应
        */
        public function debugResponse($e)
        {
            return $e->getMessage();
        }

        /**
        * @return mixed 响应
        */
        public function deployResponse()
        {
            return '哦吼，页面错误了，请联系管理员哦~';
        }
    }
    ```

  2. 在你的ThinkPHP项目中找到application/provider.php，在其中将ResponseBuilder接口绑定到你自己的实现上：
    ``` php
    <?php
    // ....

    // 应用容器绑定定义
    return [
        // 将工具的ResponseBuilder接口绑定到自定义的Builder类上
        \finntenzor\report\ResponseBuilder::class => \app\index\common\ExceptionResponseBuilder::class
    ];

    // 或者你也可以使用命令式的绑定：
    bind(\finntenzor\report\ResponseBuilder::class, \app\index\common\ExceptionResponseBuilder::class)
    ```

### 添加异常忽略类型
ThinkPHP默认忽略了“\\think\\exception\\HttpException”类型的异常，在此工具中，如果需要忽略更多类型的异常，则可以使用如下代码修改：
``` php
use finntenzor\report\ExceptionHandle;

ExceptionHandle::ignore('\\app\\index\\MyException');
```
这段代码需要放在初始化阶段加载的php文件中，例如/application/common.php
