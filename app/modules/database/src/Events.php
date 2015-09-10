<?php

namespace Pagekit\Database;

final class Events
{
    /**
     * This event occurs after an entity is loaded.
     *
     * @var string
     */
    const INIT = 'init';

    /**
     * This event occurs before an entity is saved.
     *
     * @var string
     */
    const SAVING = 'saving';

    /**
     * This event occurs after an entity is saved.
     *
     * @var string
     */
    const SAVED = 'saved';

    /**
     * This event occurs before a new entity is saved.
     *
     * @var string
     */
    const CREATING = 'creating';

    /**
     * This event occurs after a new entity is saved.
     *
     * @var string
     */
    const CREATED = 'created';

    /**
     * This event occurs before an existing entity is updated.
     *
     * @var string
     */
    const UPDATING = 'updating';

    /**
     * This event occurs after an existing entity is updated.
     *
     * @var string
     */
    const UPDATED = 'updated';

    /**
     * This event occurs before an existing entity is deleted.
     *
     * @var string
     */
    const DELETING = 'deleting';

    /**
     * This event occurs after an existing entity is deleted.
     *
     * @var string
     */
    const DELETED = 'deleted';
}
