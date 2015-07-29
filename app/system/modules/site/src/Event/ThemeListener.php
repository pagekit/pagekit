<?php

namespace Pagekit\Site\Event;

use Pagekit\System\Theme;
use Pagekit\Event\EventSubscriberInterface;

class ThemeListener implements EventSubscriberInterface
{
    /**
     * @var \Pagekit\System\Theme
     */
    protected $theme;

    /**
     * Constructor.
     *
     * @param \Pagekit\System\Theme $theme
     */
    public function __construct(Theme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Sets the node theme data.
     *
     * @param \Pagekit\Event\Event $event
     * @param \Pagekit\Site\Model\NodeInterface $node
     */
    public function onNodeInit($event, $node)
    {
        $config  = $this->theme->get("data.nodes.".$node->getId(), []);
        $default = $this->theme->config("node", []);

        $node->theme = array_replace($default, $config);
    }

    /**
     * Saves the node theme data.
     *
     * @param \Pagekit\Event\Event $event
     * @param \Pagekit\Site\Model\NodeInterface $node
     * @param array $data
     */
    public function onNodeSaved($event, $node, $data)
    {
        if (!isset($data['theme'])) {
            return;
        }

        $this->theme->options['data']['nodes'][$node->getId()] = $data['theme'];
        $this->theme->save();
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'model.node.init' => 'onNodeInit',
            'model.node.saved' => 'onNodeSaved'
        ];
    }
}
