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
        $this->aliases = $this('db.em')->getRepository('Pagekit\System\Entity\Alias');
    }

    /**
     * @Request({"filter": "array"})
     * @View("system/admin/aliases/index.razr.php")
     */
    public function indexAction($filter = null)
    {
        if ($filter) {
            $this('session')->set('alias.filter', $filter);
        } else {
            $filter = $this('session')->get('alias.filter', array());
        }

        $query = $this->aliases->query();

        if (isset($filter['search']) && strlen($filter['search'])) {
            $query->where('alias LIKE ?', array("%{$filter['search']}%"));
        }

        return array('head.title' => __('URL Aliases'), 'aliases' => $query->get(), 'filter' => $filter);
    }

    /**
     * @View("system/admin/aliases/edit.razr.php")
     */
    public function addAction()
    {
        return array('head.title' => __('Add URL Alias'), 'alias' => new Alias);
    }

    /**
     * @Request({"id": "int"})
     * @View("system/admin/aliases/edit.razr.php")
     */
    public function editAction($id)
    {
        try {

            if (!$alias = $this->aliases->find($id)) {
                throw new Exception(__('Invalid alias id.'));
            }

            return array('head.title' => __('Edit URL Alias'), 'alias' => $alias);

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
            return $this->redirect('@system/alias/index');
        }
    }

    /**
     * @Request({"id": "int", "alias", "source"})
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

            if (!$source = trim($source, '/')) {
                throw new Exception(__('Invalid source.'));
            }

            if ($this->aliases->where(array('alias = ?', 'id <> ?'), array($alias, $id))->first()) {
                throw new Exception(__('The alias "%alias%" is already in use.', array('%alias%' => $alias)));
            }

            $this->aliases->save($obj, compact('alias', 'source'));
            $id = $obj->getId();
            $this('message')->success($id ? __('Alias saved.') : __('Alias created.'));

        } catch (Exception $e) {
            $this('message')->error($e->getMessage());
        }

        return $this->redirect($id ? '@system/alias/edit' : '@system/alias/add', compact('id'));
    }

    /**
     * @Request({"ids": "int[]"})
     */
    public function deleteAction($ids = array())
    {
        foreach ($ids as $id) {
            if ($alias = $this->aliases->find($id)) {
                $this->aliases->delete($alias);
            }
        }

        $this('message')->success(_c('{0} No alias deleted.|{1} Alias deleted.|]1,Inf[ Aliases deleted.', count($ids)));

        return $this->redirect('@system/alias/index');
    }
}
