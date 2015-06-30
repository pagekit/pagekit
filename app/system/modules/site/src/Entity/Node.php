<?php

namespace Pagekit\Site\Entity;

use Pagekit\Database\ORM\ModelTrait;
use Pagekit\Site\Model\NodeInterface;
use Pagekit\System\Entity\DataTrait;
use Pagekit\System\Entity\NodeTrait;
use Pagekit\User\Entity\AccessTrait;

/**
 * @Entity(tableClass="@system_node", eventPrefix="site.node")
 */
class Node implements NodeInterface, \JsonSerializable
{
    use AccessTrait, DataTrait, ModelTrait, NodeTrait;

    /** @Column(type="integer") @Id */
    protected $id;

    /** @Column(name="parent_id", type="integer") */
    protected $parentId = 0;

    /** @Column(type="integer") */
    protected $priority = 0;

    /** @Column(type="integer") */
    protected $status = 0;

    /** @Column(type="string") */
    protected $slug;

    /** @Column(type="string") */
    protected $path;

    /** @Column(type="string") */
    protected $title;

    /** @Column(type="string") */
    protected $type;

    /** @Column(type="string") */
    protected $menu = '';

    /** @Column(type="json_array") */
    protected $data;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getMenu()
    {
        return $this->menu;
    }

    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @PreSave
     */
    public function preSave()
    {
        $db = self::getConnection();

        $i  = 2;
        $id = $this->id;

        if (!$this->slug) {
            $this->slug = $this->getTitle();
        }

        while (self::where(['slug = ?', 'parent_id= ?'], [$this->slug, $this->parentId])->where(function ($query) use ($id) {
            if ($id) $query->where('id <> ?', [$id]);
        })->first()) {
            $this->slug = preg_replace('/-\d+$/', '', $this->slug).'-'.$i++;
        }

        // Update own path
        $path = '/'.$this->getSlug();
        if ($this->parentId && $parent = self::find($this->parentId) and $parent->getMenu() === $this->menu) {
            $path = $parent->getPath().$path;
        } else {
            // set Parent to 0, if old parent is not found
            $this->setParentId(0);
        }
        $this->setPath($path);

        if ($this->id) {
            // Update children's paths
            foreach (self::where(['parent_id' => $this->id])->get() as $child) {
                if (0 !== strpos($child->getPath(), $this->path.'/') || $this->getMenu() !== $this->menu) {
                    $child->setMenu($this->menu);
                    $child->save();
                }
            }
        } else {
            // Set priority
            $this->priority = 1 + $db->createQueryBuilder()
                    ->select($db->getDatabasePlatform()->getMaxExpression('priority'))
                    ->from('@system_node')
                    ->where(['parent_id' => $this->parentId])
                    ->execute()
                    ->fetchColumn();
        }
    }

    /**
     * @PreDelete
     */
    public function preDelete()
    {
        // Update children's parents
        foreach (self::where('parent_id = ?', [$this->id])->get() as $child) {
            $child->setParentId($this->parentId);
            $child->save();
        }
    }

    /**
     * Gets the node's link.
     *
     * @param  string $url
     * @return string
     */
    public function getLink($url = '')
    {
        return $this->parseQuery($this->get('url', $url), $this->get('variables', []));
    }

    /**
     * Parses query parameters into a URL.
     *
     * @param  string $url
     * @param  array  $parameters
     * @return string
     */
    protected function parseQuery($url, $parameters = [])
    {
        if ($query = substr(strstr($url, '?'), 1)) {
            parse_str($query, $params);
            $url        = strstr($url, '?', true);
            $parameters = array_replace($parameters, $params);
        }

        if ($query = http_build_query($parameters, '', '&')) {
            $url .= '?'.$query;
        }

        return $url;
    }

    /**
     * Sets parent_id of orphaned nodes to zero.
     *
     * @return int
     */
    public static function fixOrphanedNodes()
    {
        if ($orphaned = self::getConnection()
            ->createQueryBuilder()
            ->from('@system_node n')
            ->leftJoin('@system_node c', 'c.id = n.parent_id AND c.menu = n.menu')
            ->where(['n.parent_id <> 0', 'c.id IS NULL'])
            ->execute('n.id')->fetchAll(\PDO::FETCH_COLUMN)
        ) {
            Node::query()
                ->whereIn('id', $orphaned)
                ->update(['parent_id' => 0]);
        }
    }
}
