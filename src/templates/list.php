<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>错误报告</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <h1>错误报告</h1>
        <table class="table">
            <tr>
                <th>日期</th>
                <th>操作</th>
            </tr>
<?php foreach ($list as $item) { ?>
            <tr>
                <td><?php echo $item['date']?></td>
                <td><a href="<?php echo $item['url']?>">查看</a></td>
            </tr>
<?php } ?>
        </table>
    </div>
</body>
</html>
