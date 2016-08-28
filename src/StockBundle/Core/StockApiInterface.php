<?php

namespace StockBundle\Core;


interface StockApiInterface
{
    /**
     * Get historical data for a symbol
     *
     * @param string $symbol
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    public function getHistoricalData($symbol, \DateTime $startDate, \DateTime $endDate);

    /**
     * Get quotes for one or multiple symbols
     *
     * @param array|string $symbols
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    public function getQuotes($symbols);

    /**
     * Get quotes list for one or multiple symbols
     *
     * @param array|string $symbols
     * @return array
     * @throws \Scheb\YahooFinanceApi\Exception\ApiException
     */
    public function getQuotesList($symbols);
}