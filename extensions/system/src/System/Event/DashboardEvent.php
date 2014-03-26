<?php

namespace Pagekit\System\Event;

use Pagekit\Framework\Event\Event;
use Pagekit\Widget\Model\TypeInterface;
use Pagekit\Widget\Model\TypeManager;

class DashboardEvent extends Event
{
    /**
     * @var TypeManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param TypeManager $manager
     */
    public function __construct(TypeManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return TypeManager
     */
    public function getTypeManager()
    {
        return $this->manager;
    }

    /**
     * @param string|TypeInterface $type
     */
    public function registerType($type)
    {
        $this->manager->register($type);
    }
}