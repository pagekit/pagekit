<?php

namespace Pagekit\System\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\System\Entity\Alias;

/**
 * @Access("system: manage url aliases", admin=true)
 */
class AliasController extends Controller
{
    /**
     * @var Repository
     */
    protected $aliases;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->aliases = $this['db.em']->getRepository('Pagekit\System\Entity\Alias');
    }

    /**
     * @Request({"filter": "array"})
     * @Response("extension://system/views/admin/aliases/index.razr")
     */
    public function indexAction($filter = null)
    {
        if ($filter) {
            $this['session']->set('alias.filter', $filter);
        } else {
            $filter = $this['session']->get('alias.filter', []);
        }

        $query = $this->aliases->query();

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where('alias LIKE ?', ["%{$filter['search']}%"]);
        }

        return ['head.title' => __('URL Aliases'), 'aliases' => $query->get(), 'filter' => $filter];
    }

    /**
     * @Response("extension://system/views/admin/aliases/edit.razr")
     */
    public function addAction()
    {
        return ['head.title' => __('Add URL Alias'), 'alias' => new Alias];
    }

    /**
     * @Request({"id": "int"})
     * @Response("extension://system/views/admin/aliases/edit.razr")
     */
    public function editAction($id)
    {
        try {

            if (!$alias = $this->aliases->find($id)) {
                throw new Exception(__('Invalid alias id.'));
            }

            return ['head.title' => __('Edit URL Alias'), 'alias' => $alias];

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
            return $this->redirect('@system/alias');
        }
    }

    /**
     * @Request({"id": "int", "alias", "source"}, csrf=true)
     */
    public function saveAction($id, $alias, $source)
    {
        try {

            if (!$obj = $this->aliases->find($id)) {
                $obj = new Alias;
            }

            if (!$alias = trim($alias, '/')) {
                throw new Exception(__('Invalid alias.'));
            }

            if (!$source = trim($source, '/') or strpos($source, '@') !== 0) {
                throw new Exception(__('Invalid source.'));
            }

            if ($this->aliases->where(['alias = ?', 'id <> ?'], [$alias, $id])->first()) {
                throw new Exception(__('The alias "%alias%" is already in use.', ['%alias%' => $alias]));
            }

            $this->aliases->save($obj, compact('alias', 'source'));
            $id = $obj->getId();
            $this['message']->success($id ? __('Alias saved.') : __('Alias created.'));

        } catch (Exception $e) {
            $this['message']->error($e->getMessage());
        }

        return $this->redirect($id ? '@system/alias/edit' : '@system/alias/add', compact('id'));
    }

    /**
     * @Request({"ids": "int[]"}, csrf=true)
     */
    public function deleteAction($ids = [])
    {
        foreach ($ids as $id) {
            if ($alias = $this->aliases->find($id)) {
                $this->aliases->delete($alias);
            }
        }

        $this['message']->success(_c('{0} No alias deleted.|{1} Alias deleted.|]1,Inf[ Aliases deleted.', count($ids)));

        return $this->redirect('@system/alias');
    }
}
