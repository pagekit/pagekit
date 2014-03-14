@trans('Hi') @user.username<br>
<a href="@url.route('@system/auth/logout', ['redirect' => widget.get('redirect.logout')])">@trans('Logout')</a>