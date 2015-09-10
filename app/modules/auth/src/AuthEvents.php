<?php

namespace Pagekit\Auth;

final class AuthEvents
{
    /**
     * This event occurs before a user is authenticated.
     *
     * @var string
     */
    const PRE_AUTHENTICATE = 'auth.pre_authenticate';

    /**
     * This event occurs after a user is authenticated.
     *
     * @var string
     */
    const SUCCESS = 'auth.success';

    /**
     * This event occurs after a user cannot be authenticated.
     *
     * @var string
     */
    const FAILURE = 'auth.failure';

    /**
     * This event occurs when a user needs to be authorized.
     *
     * @var string
     */
    const AUTHORIZE = 'auth.authorize';

    /**
     * This event occurs after a user is logged in interactively for authentication based on http, cookies or X509.
     *
     * @var string
     */
    const LOGIN = 'auth.login';

    /**
     * This event occurs after a user is logged out.
     *
     * @var string
     */
    const LOGOUT = 'auth.logout';
}
