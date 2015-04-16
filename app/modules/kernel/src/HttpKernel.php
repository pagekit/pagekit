<?php

namespace Pagekit\Kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel as BaseKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HttpKernel extends BaseKernel
{
    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if ($type === HttpKernelInterface::MASTER_REQUEST) {
            $this->dispatcher->dispatch('kernel.boot');
        }

        return parent::handle($request, $type, $catch);
    }
}
