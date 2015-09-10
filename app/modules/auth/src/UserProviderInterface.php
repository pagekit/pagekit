<?php

namespace Pagekit\Auth;

interface UserProviderInterface
{
    /**
     * Retrieve a user by its unique identifier.
     *
     * @param  string $id
     * @return UserInterface|null
     */
    public function find($id);

    /**
     * Retrieve a user by their unique username.
     *
     * @param  string $username
     * @return UserInterface|null
     */
    public function findByUsername($username);
    
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return UserInterface|null
     */
    public function findByCredentials(array $credentials);

    /**
     * Validate a user against the given credentials.
     *
     * @param  UserInterface $user
     * @param  array         $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials);
}
