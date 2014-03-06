<?php

namespace Pagekit\System\Event;

use Pagekit\Component\Event\Event;
use Pagekit\Component\View\Widget\Model\TypeInterface;
use Pagekit\Component\View\Widget\Model\TypeManager;

class DashboardInitEvent extends Event
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