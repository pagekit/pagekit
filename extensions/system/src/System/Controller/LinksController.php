<?php

namespace Pagekit\System\Controller;

use Pagekit\Framework\Controller\Controller;
use Pagekit\Framework\Controller\Exception;
use Pagekit\System\Link\LinkManager;

/**
 * @Access(admin=true)
 */
class LinksController extends Controller
{
    /**
     * @var LinkManager
     */
    protected $links;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->links = $this('links');
    }

    public function indexAction()
    {
        try {

            $links = array();

            foreach($this->links as $link) {
                $links[$link->getRoute()] = array(
                    'label' => $link->getLabel(),
                    'form'  => $link->renderForm()
                );
            }

            return $this('response')->json($links);

        } catch (Exception $e) {}
    }
}
