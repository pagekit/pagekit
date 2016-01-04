<?php

namespace Pagekit\Debug\DataCollector;

use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\Storage\StorageInterface;

class ProfileDataCollector implements DataCollectorInterface
{
    protected $storage;

    /**
     * Constructor.
     *
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        return ['requests' => $this->storage->find()];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'profile';
    }
}
