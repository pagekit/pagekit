<?php

namespace Pagekit\Menu;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Component\Menu\MenuProvider as BaseProvider;
use Pagekit\Component\Menu\Model\FilterManager;
use Pagekit\Component\Menu\Model\MenuInterface;
use Pagekit\Framework\Application;
use Pagekit\Menu\Entity\ItemRepository;
use Pagekit\Menu\Filter\FilterIterator;

class MenuProvider extends BaseProvider
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var MenuInterface[]
     */
    protected $loaded;

    /**
     * Constructor.
     *
     * @param Application   $app
     * @param FilterManager $filters
     */
    public function __construct(Application $app, FilterManager $filters = null)
    {
        parent::__construct($filters);

        $this->app = $app;

        FilterIterator::setApplication($app);
    }

    /**
     * @return Repository
     */
    public function getMenuRepository()
    {
        return $this->app['db.em']->getRepository('Pagekit\Menu\Entity\Menu');
    }

    /**
     * @return ItemRepository
     */
    public function getItemRepository()
    {
        return $this->app['db.em']->getRepository('Pagekit\Menu\Entity\Item');
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $this->load();

        foreach (array_keys($this->loaded) as $id) {
            $this->has($id);
        }
        return parent::getIterator();
    }

    protected function load()
    {
        if (!$this->loaded) {
            $this->loaded = $this->getMenuRepository()->findAll();
        }
    }
}
