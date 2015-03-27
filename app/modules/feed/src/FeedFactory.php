<?php

namespace Pagekit\Feed;

class FeedFactory
{
    /**
     * @var string[]
     */
    protected $feeds = [
        Feed::ATOM => 'Pagekit\Feed\Feed\Atom',
        Feed::RSS1 => 'Pagekit\Feed\Feed\RSS1',
        Feed::RSS2 => 'Pagekit\Feed\Feed\RSS2'
    ];

    /**
     * Creates a feed.
     *
     * @param  string $type
     * @param  array  $elements
     * @return FeedInterface
     */
    public function create($type = null, array $elements = [])
    {
        $class = isset($this->feeds[$type]) ? $this->feeds[$type] : $this->feeds[Feed::RSS2];
        return (new $class)->addElements($elements);
    }

    /**
     * Registers a new feed type.
     *
     * @param string $type
     * @param string $class
     */
    public function register($type, $class)
    {
        if (!is_string($class) || !is_subclass_of($class, 'Pagekit\Feed\FeedInterface')) {
            throw new \InvalidArgumentException(sprintf('Given type class "%s" is not of type Pagekit\Feed\FeedInterface', (string) $class));
        }

        $this->feeds[$type] = $class;
    }
}
