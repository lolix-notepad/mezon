<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>{title}</title>
        <script src="{conf/@mezon-http-path}/include/js/jquery-2.1.1.min.js"></script>
        <script src="{conf/@mezon-http-path}/include/js/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="{conf/@mezon-http-path}/res/css/jquery-ui.css">
        <link rel="stylesheet" href="{conf/@mezon-http-path}/res/css/basic.css">
        <link rel="shortcut icon" href="{conf/res/images/favicon}" type="image/x-icon">{view/custom-resources}

        <script>
            jQuery(
                function()
                {
                    jQuery( "#dialog-modal" ).dialog(
                        {
                            height : 'auto' , 
                            modal : true , 
                            width : 'auto' , 
                            buttons :
                            {
                                'Вход' : function()
                                {
                                    jQuery( '#login-form' ).submit();
                                }
                            }
                        }
                    );
                    jQuery( '.ui-dialog-titlebar-close' ).remove();
                }
            );
            function            enter_processor( e , Id )
            {
                if( e.keyCode == 13 )
                {
                    document.getElementById( Id ).submit();
                }
            }
        </script>
    </head>

    <body>
        <div id="dialog-modal" title="{title}" style="display: none;">
            <form id="login-form" method="post">
                <table width="140">
                    <tr>
                        <td>Логин</td><td><input type="text" name="login"></td>
                    </tr>
                    <tr>
                        <td>Пароль</td><td><input type="password" name="password" onkeyup="enter_processor( event , 'login-form' );"></td>
                    </tr>
                </table>
            </form>
        </div>
    </body>
</html>