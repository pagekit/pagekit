<?php

namespace Pagekit\Templating\Helper;

use Pagekit\View\Section\SectionManager;
use Symfony\Component\Templating\Helper\Helper;

class SectionHelper extends Helper
{
    protected $sections;

    public function __construct(SectionManager $sections)
    {
        $this->sections = $sections;
    }

    /**
     * Proxy method call to sections manager.
     *
     * @param  string $method
     * @param  array  $args
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->sections, $method)) {
            throw new \BadMethodCallException(sprintf('Undefined method call "%s::%s"', get_class($this->sections), $method));
        }

        return call_user_func_array([$this->sections, $method], $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sections';
    }
}
