<?php

namespace Pagekit\Routing\Request;

use Pagekit\Event\EventSubscriberInterface;

class ParamFetcherListener implements EventSubscriberInterface
{
    /**
     * @var ParamFetcherInterface
     */
    protected $paramFetcher;

    /**
     * Constructor.
     *
     * @param ParamFetcherInterface $paramFetcher
     */
    public function __construct(ParamFetcherInterface $paramFetcher = null)
    {
        $this->paramFetcher = $paramFetcher ?: new ParamFetcher;
    }

    /**
     * Maps the parameters to request attributes.
     *
     * @param $event
     */
    public function onController($event, $request)
    {
        $controller = $event->getController();
        $parameters = $request->attributes->get('_request[value]', [], true);
        $options    = $request->attributes->get('_request[options]', [], true);

        if (is_array($controller) && is_array($parameters)) {

            $this->paramFetcher->setRequest($request);
            $this->paramFetcher->setParameters($parameters, $options);

            $r = new \ReflectionMethod($controller[0], $controller[1]);

            foreach ($r->getParameters() as $index => $param) {
                if (null !== $value = $this->paramFetcher->get($index)) {
                    $request->attributes->set($param->getName(), $value);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'controller' => ['onController', 110]
        ];
    }
}
