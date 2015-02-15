<?php

namespace Pagekit\User\Controller\Api;

use Pagekit\Application as App;
use Pagekit\Application\Controller;
use Pagekit\User\Entity\Role;

/**
 * @Access("system: manage user permissions")
 * @Response("json")
 */
class RoleController extends Controller
{
    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        return Role::findAll();
    }

    /**
     * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
     */
    public function getAction($id)
    {
        return Role::find($id);
    }

    /**
     * @Route("/", methods="POST")
     * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
     * @Request({"id": "int", "name", "permissions": "array"}, csrf=true)
     */
    public function saveAction($id, $name = '', $permissions = [])
    {
        // is new ?
        if (!$role = Role::find($id)) {
            $role = new Role;
        }

        if ($name !== '') {
            $role->setName($name);
        }

        $role->setPermissions($permissions);
        $role->save();

        return $role;
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id = 0)
    {
        if ($role = Role::find($id)) {
            $role->delete();
        }

        return $this->redirect('@system/role');
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"roles": "json"}, csrf=true)
     */
    public function bulkSaveAction($roles = [])
    {
        foreach ($roles as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return Role::findAll();
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "json"}, csrf=true)
     */
    public function bulkDeleteAction($ids = [])
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return Role::findAll();
    }
}
