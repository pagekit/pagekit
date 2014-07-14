<?php

namespace Pagekit\Menu;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\ApplicationTrait;
use Pagekit\Menu\Entity\ItemRepository;
use Pagekit\Menu\Filter\FilterIterator;
use Pagekit\Menu\Model\FilterManager;
use Pagekit\Menu\Model\MenuInterface;
use Pagekit\Menu\Model\Node;

class MenuProvider implements \IteratorAggregate, \ArrayAccess
{
    use ApplicationTrait;

    /**
     * @var MenuInterface[]
     */
    protected $menus = [];

    /**
     * @var MenuInterface[]
     */
    protected $loaded;

    /**
     * @var FilterManager
     */
    protected $filters;

    /**
     * Constructor.
     *
     * @param FilterManager $filters
     */
    public function __construct(FilterManager $filters = null)
    {
        $this->filters = $filters ?: new FilterManager;

        FilterIterator::setApplication($this->getApplication());
    }

    /**
     * @return Repository
     */
    public function getMenuRepository()
    {
        return $this['db.em']->getRepository('Pagekit\Menu\Entity\Menu');
    }

    /**
     * @return ItemRepository
     */
    public function getItemRepository()
    {
        return $this['db.em']->getRepository('Pagekit\Menu\Entity\Item');
    }

    /**
     * Checks whether a menu is registered.
     */
    public function has($id)
    {
        if (isset($this->menus[$id])) {
            return true;
        }

        $this->load();

        if (isset($this->loaded[$id])) {
            $this->menus[$id] = $this->loaded[$id];
            $this->menus[$id]->setItems($this->getItemRepository()->findByMenu($this->loaded[$id]));
        }

        return isset($this->menus[$id]);
    }

    /**
     * Gets a menu.
     *
     * @param  string $id
     * @return MenuInterface
     */
    public function get($id)
    {
        return $this->has($id) ? $this->menus[$id] : null;
    }

    /**
     * @return FilterManager
     */
    public function getFilterManager()
    {
        return $this->filters;
    }

    /**
     * {@see FilterManager::register}
     */
    public function registerFilter($name, $filter, $priority = 0)
    {
        $this->filters->register($name, $filter, $priority);
    }

    /**
     * Retrieves menu item tree.
     *
     * @param  string|MenuInterface $menu
     * @param  array                $parameters
     * @return Node
     */
    public function getTree($menu, array $parameters = [])
    {
        if (!$menu instanceof MenuInterface) {
            $menu = $this->get($menu);
        }
        $iterator = $menu->getIterator();

        foreach ($this->filters as $filters) {
            foreach ($filters as $class) {
                $iterator = new $class($iterator, $parameters);
            }
        }

        $items = [new Node(0)];

        foreach ($iterator as $item) {
            $id   = $item->getId();
            $pid  = $item->getParentId();

            if (!isset($items[$id])) {
                $items[$id] = new Node($id);
            }

            $items[$id]->setItem($item);

            if (!isset($items[$pid])) {
                $items[$pid] = new Node($pid);
            }

            $items[$pid]->add($items[$id]);
        }

        return $items[isset($parameters['root'], $items[$parameters['root']]) ? $parameters['root'] : 0];
    }

    /**
     * Implements the IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->load();

        foreach (array_keys($this->loaded) as $id) {
            $this->has($id);
        }

        return new \ArrayIterator($this->menus);
    }

    protected function load()
    {
        if (!$this->loaded) {
            $this->loaded = $this->getMenuRepository()->findAll();
        }
    }
}
