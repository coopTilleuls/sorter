<?php

declare(strict_types=1);

namespace Sorter\Extension\Symfony\Bundle;

use Sorter\Extension\Symfony\Bundle\DependencyInjection\Compiler\ApplierCompilerPass;
use Sorter\Extension\Symfony\Bundle\DependencyInjection\Compiler\RequestHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SorterBundle extends Bundle
{
    #[\Override]
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ApplierCompilerPass());
        $container->addCompilerPass(new RequestHandlerPass());
    }
}
