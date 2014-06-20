<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>

        <p>@trans('Hello'),</p>

        <p>@trans('A new user has registered at %site%! The user has verified his email, and requests that you approve his account.', ['%site%' => app.option.get('system:app.site_title')])</p>

        <p>@trans('User Details:<br><br>Name: %name%<br>Username: %username%<br>Email: %email%', ['site' => app.option.get('system:app.site_title'), '%name%' => user.name, '%username%' => user.username, '%email%' => user.email])</p>

        <p>@trans('To activate the account click on:')</p>

        @set(link = url.route('@system/registration/activate', ['user' => user.username, 'key' => user.activation], true))
        <p><a target="_blank" href="@link">@link</a></p>

    </body>
</html>