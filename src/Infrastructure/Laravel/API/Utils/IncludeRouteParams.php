<?php

namespace Project\Infrastructure\Laravel\API\Utils;

trait IncludeRouteParams
{
    public function all($keys = null)
    {
        foreach ($this->route()->parameters() as $key => $value) {
            $this->offsetSet($key, $value);
        }

        return parent::all($keys);
    }
}