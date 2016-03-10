<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <script src="{conf/@mezon-http-path}/include/js/jquery-2.1.1.min.js"></script>
        <script src="{conf/@mezon-http-path}/include/js/jquery-ui.min.js"></script>
        <script src="{conf/@mezon-http-path}/include/js/application.js"></script>
        <link rel="stylesheet" href="{conf/@mezon-http-path}/res/css/jquery-ui.css">
        <link rel="stylesheet" href="{conf/@mezon-http-path}/res/css/basic.css">
        <link rel="shortcut icon" href="{conf/res/images/favicon}" type="image/x-icon">{view/custom-resources}
    </head>

    <body>
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Главная</a></li>
            </ul>
            <div id="tabs-1">
                {view/template/main-tab}
            </div>
        </div>
        <div id="result-dialog" class="hidden" title="Результат операции">
        </div>
        <div id="shure-dialog" class="hidden" title="Вы уверены?">
        </div>
    </body>
</html>