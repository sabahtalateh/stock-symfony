<?php

namespace StockBundle\Core;


class Stock
{
    /**
     * @var StockApiInterface
     */
    private $api;

    /**
     * Stock constructor.
     * @param StockApiInterface $apiClient
     */
    public function __construct(StockApiInterface $apiClient)
    {
        $this->api = $apiClient;
    }

    public function getHistoricalQuotes($symbol, \DateTime $startDate, \DateTime $endDate)
    {
        return $this->api->getHistoricalData($symbol, $startDate, $endDate)['query'];
    }

    public function getQuotesList(array $quotes)
    {
        return $this->api->getQuotesList($quotes)['query']['results']['quote'];
    }
}