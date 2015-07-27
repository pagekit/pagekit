<?php

namespace Pagekit\User\Controller;

use Pagekit\Application as App;
use Pagekit\User\Model\Role;

/**
 * @Access("user: manage user permissions")
 */
class RoleApiController
{
    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        return array_values(Role::findAll());
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
     * @Request({"role": "array", "id": "int"}, csrf=true)
     */
    public function saveAction($data, $id = 0)
    {
        // is new ?
        if (!$role = Role::find($id)) {

            if ($id) {
                App::abort(404, __('Role not found.'));
            }

            $role = Role::create();
        }

        $role->save($data);

        return ['message' => 'success', 'role' => $role];
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

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"roles": "array"}, csrf=true)
     */
    public function bulkSaveAction($roles = [])
    {
        foreach ($roles as $data) {
            $this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="DELETE")
     * @Request({"ids": "array"}, csrf=true)
     */
    public function bulkDeleteAction($ids = [])
    {
        foreach (array_filter($ids) as $id) {
            $this->deleteAction($id);
        }

        return ['message' => 'success'];
    }
}
