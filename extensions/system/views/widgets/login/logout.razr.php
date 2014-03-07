@trans('Hi') @user.username<br>
<a href="@url('@system/auth/logout', ['redirect' => widget.get('redirect.logout')])">@trans('Logout')</a>