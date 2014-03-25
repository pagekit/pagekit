<?php

namespace Pagekit\User\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;
use Pagekit\User\Event\PermissionEvent;

/**
 * @Route("/system/user/permission")
 * @Access("system: manage user permissions", admin=true)
 */
class PermissionController extends Controller
{
    /**
     * @var Repository
     */
    protected $roles;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->roles = $this('users')->getRoleRepository();
    }

    /**
     * @View("system/admin/user/permission.razr.php")
     */
    public function indexAction()
    {
        $roles = $this->roles->query()->orderBy('priority')->get();

        $this('events')->trigger('admin.permission', $event = new PermissionEvent);

        return array('head.title' => __('Permissions'), 'roles' => $roles, 'permissions' => $event->getPermissions());
    }

    /**
     * @Request({"permissions": "array"})
     * @Token
     */
    public function saveAction($permissions = array())
    {
        foreach ($this->roles->findAll() as $role) {
            $role->setPermissions(isset($permissions[$role->getId()]) ? $permissions[$role->getId()] : array());
            $this->roles->save($role);
        }

        return $this('request')->isXmlHttpRequest() ? $this('response')->json(array('message' => __('Permissions saved!'))) : $this->redirect('@system/permission/index');
    }
}
