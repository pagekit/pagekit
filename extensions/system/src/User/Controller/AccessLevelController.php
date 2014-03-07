<?php

namespace Pagekit\User\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\User\Entity\AccessLevel;

/**
 * @Route("/system/user/access")
 * @Access("system: manage user permissions", admin=true)
 */
class AccessLevelController extends Controller
{
    /**
     * @var Repository
     */
    protected $levels;

    /**
     * @var Repository
     */
    protected $roles;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->levels = $this('users')->getAccessLevelRepository();
        $this->roles  = $this('users')->getRoleRepository();
    }

    /**
     * @Request({"id": "int"})
     * @View("system/admin/user/access.razr.php")
     */
    public function indexAction($id = null)
    {
        $levels = $this->levels->query()->orderBy('priority')->get();
        $roles  = $this->roles->query()->orderBy('priority')->get();

        if ($id === null && count($levels)) {
            $level = current($levels);
        } elseif ($id && isset($levels[$id])) {
            $level = $levels[$id];
        } else {
            $level = new AccessLevel;
            $level->setId(0);
        }

        return array('head.title' => __('Access Levels'), 'level' => $level, 'levels' => $levels, 'roles' => $roles);
    }

    /**
     * @Request({"id": "int", "name", "roles": "array"})
     * @Token
     */
    public function saveAction($id, $name = '', $roles = null)
    {
        // is new ?
        if (!$level = $this->levels->find($id)) {
            $level = new AccessLevel;
        }

        if ($name !== '') {
            $level->setName($name);
        }

        if ($roles !== null) {
            $level->setRoles(array_filter($roles));
        }

        $this->levels->save($level);

        return $this('request')->isXmlHttpRequest() ? $this('response')->json(array("message"=>__('Access level saved!'))) : $this->redirect('@system/accesslevel/index', array('id' => isset($level) ? $level->getId() : 0));
    }

    /**
     * @Request({"id": "int"})
     * @Token
     */
    public function deleteAction($id = 0)
    {
        if ($level = $this->levels->find($id)) {
            $this->levels->delete($level);
        }

        return $this->redirect('@system/accesslevel/index');
    }

    /**
     * @Request({"order": "array"})
     */
    public function priorityUpdateAction($order) {

        foreach ($order as $id => $priority) {

            $level = $this->levels->find($id);

            if ($level) {
                $this->levels->save($level, compact('priority'));
            }
        }

        return $this('response')->json($order);
    }
}
