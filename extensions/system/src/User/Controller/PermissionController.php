<?php

namespace Pagekit\User\Controller;

use Pagekit\Component\Database\ORM\Repository;
use Pagekit\Framework\Controller\Controller;

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
        $this->roles = $this['users']->getRoleRepository();
    }

    /**
     * @Response("extension://system/views/admin/user/permission.razr")
     */
    public function indexAction()
    {
        $roles = $this->roles->query()->orderBy('priority')->get();

        return ['head.title' => __('Permissions'), 'roles' => $roles, 'permissions' => $this['permissions']];
    }

    /**
     * @Request({"permissions": "array"}, csrf=true)
     * @Response("json")
     */
    public function saveAction($permissions = [])
    {
        foreach ($this->roles->findAll() as $role) {
            $role->setPermissions(isset($permissions[$role->getId()]) ? $permissions[$role->getId()] : []);
            $this->roles->save($role);
        }

        return $this['request']->isXmlHttpRequest() ? ['message' => __('Permissions saved!')] : $this->redirect('@system/permission');
    }
}
