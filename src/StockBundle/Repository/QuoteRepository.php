<?php

namespace StockBundle\Repository;
use StockBundle\Entity\Quote;

/**
 * QuoteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuoteRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllSymbols()
    {
        return array_map(function ($quote) {
            /** @var Quote $quote */
            return $quote->getSymbol();
        }, $this->findAll());
    }
}
