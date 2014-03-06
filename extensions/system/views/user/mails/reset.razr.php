<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body>

		<p>@trans('Someone requested that the password be reset for the following account'):</p>

		<p><a target="_blank" href="@url.root">@url.root</a></p>

		<p>@trans('Username'): @username</p>

		<p>@trans('If this was a mistake, just ignore this email and nothing will happen.')</p>

		<p>@trans('To reset your password, visit the following address'):</p>

		<p><a target="_blank" href="@url.confirm">@url.confirm</a></p>

	</body>
</html>