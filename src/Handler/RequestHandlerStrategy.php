<?php

namespace Sorter\Handler;

interface RequestHandlerStrategy extends RequestHandler
{
    public function supports(mixed $request): bool;
}
