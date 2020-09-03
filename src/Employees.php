<?php

namespace Business;

class Employees extends ClientApi
{

    /**
     * {@inheritDoc}
     */
    public function path()
    {
        return '/api/rest/employees.json';
    }
}