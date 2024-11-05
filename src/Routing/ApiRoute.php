<?php
// ernicani/Routing/ApiRoute.php

namespace Ernicani\Routing;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class ApiRoute
{
    public string $entity;
    public string $path;

    public function __construct(string $entity, string $path)
    {
        $this->entity = $entity;
        $this->path = "api/"."$path";
    }
}
