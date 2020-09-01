<?php

namespace Business;

class CustomerOrders
{
    /**
     * The authentication credentials.
     *
     * @var \Business\Auth
     */
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

}
