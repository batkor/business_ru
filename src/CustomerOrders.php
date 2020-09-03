<?php

namespace Business;

class CustomerOrders extends ClientApi
{

    public function get(array $params = [])
    {
        return $this->request('get', $params);
    }

    public function create(array $params = [])
    {
        return $this->request('post', $params);
    }

    /**
     * {@inheritDoc}
     */
    public function path()
    {
        return '/api/rest/customerorders.json';
    }
}
