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
        $this->roles = $this('users')->getRoleRepository();
    }

    /**
     * @View("system/admin/user/permission.razr.php")
     */
    public function indexAction()
    {
        $roles = $this->roles->query()->orderBy('priority')->get();

        return array('meta.title' => __('Permissions'), 'roles' => $roles);
    }

    /**
     * @Request({"permissions": "array"})
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
