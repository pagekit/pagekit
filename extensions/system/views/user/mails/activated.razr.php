<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body>

        <p>@trans('Hello %name%', ['%name%' => user.name]),</p>

        <p>@trans('Your account has been activated by an administrator.')</p>

        <p>@trans('You may login with the username:') @user.username</p>

        <p><a target="_blank" href="@url.route('@system/auth/login', [], true)">@url.route('@system/auth/login', [], true)</a></p>

    </body>
</html>