<?php

namespace Pagekit\Database;

final class Events
{
    /**
     * This event occurs after the database was connected.
     *
     * @var string
     */
    const postConnect = 'postConnect';

    /**
     * This event occurs before an entity is loaded.
     *
     * @var string
     */
    const postLoad = 'postLoad';

    /**
     * This event occurs before an entity is saved.
     *
     * @var string
     */
    const preSave = 'preSave';

    /**
     * This event occurs after an entity is saved.
     *
     * @var string
     */
    const postSave = 'postSave';

    /**
     * This event occurs before a new entity is saved.
     *
     * @var string
     */
    const preCreate = 'preCreate';

    /**
     * This event occurs after a new entity is saved.
     *
     * @var string
     */
    const postCreate = 'postCreate';

    /**
     * This event occurs before an existing entity is updated.
     *
     * @var string
     */
    const preUpdate = 'preUpdate';

    /**
     * This event occurs after an existing entity is updated.
     *
     * @var string
     */
    const postUpdate = 'postUpdate';

    /**
     * This event occurs before an existing entity is deleted.
     *
     * @var string
     */
    const preDelete = 'preDelete';

    /**
     * This event occurs after an existing entity is deleted.
     *
     * @var string
     */
    const postDelete = 'postDelete';
}
