<?php

namespace Business;

interface ClientApiInterface
{

    /**
     * The path mask for build uri by action.
     */
    public const PATH_MASK = '/api/rest/%s.json';

    /**
     * The customer order action.
     *
     * @see https://developers.business.ru/api-polnoe/zakazy_pokupatelej/234
     */
    public const ACTION_CUSTOMER_ORDER = 'customerorders';

    /**
     * The customer order action.
     *
     * @see https://developers.business.ru/api-polnoe/zakazy_pokupatelej/234
     */
    public const ACTION_EMPLOYEES = 'employees';

    /**
     * Request to resource.
     *
     * @param string $method
     *   The request method.
     * @param array $params
     *   The params for request.
     *
     * @return mixed
     *   The request result.
     */
    public function request($method, array $params);

    /**
     * Returns path to resource.
     *
     * @return string
     */
    public function path();

}