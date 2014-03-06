@trans('Hi') @user.username<br>
<a href="@url.to('@system/auth/logout', ['redirect' => widget.get('redirect.logout')])">@trans('Logout')</a>