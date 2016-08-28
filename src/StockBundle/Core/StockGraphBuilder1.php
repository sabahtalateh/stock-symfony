<?php

namespace StockBundle\Core;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use StockBundle\Entity\Portfolio;
use StockBundle\Entity\PortfolioSnapshot;
use StockBundle\Entity\QuoteChar;

class StockGraphBuilder1
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    public function build(Portfolio $portfolio, \DateTime $start, \DateTime $end = null)
    {
        if ($end == null) $end = new \DateTime();

        $statement = $this->em->getConnection()->prepare("
            SELECT
              Snapshot.portfolio_id,
              Snapshot.quote_id,
              Snapshot.snapshot_id,
              Snapshot.amount,
              Snapshot.moment,
              Quote.adj_close,
              Quote.date
            FROM portfolio_snapshot AS Snapshot
              LEFT JOIN quote_char AS Quote
                ON DATE(Snapshot.moment) = Quote.date
                   AND Snapshot.quote_id = Quote.quote_id
            WHERE Snapshot.portfolio_id = :portfolio_id
                AND DATE(Snapshot.moment) >= :startDate
                AND DATE(Snapshot.moment) <= :endDate
            ORDER BY Snapshot.moment DESC
        ");

        $params = [
            'portfolio_id' => $portfolio->getId(),
            'startDate' => $start->format('Y-m-d'),
            'endDate' => $end->format('Y-m-d'),
        ];
        $statement->execute($params);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $statement->fetchAll();

        $result = $this->fillNullDatesAndCosts($result, $portfolio, $start, $end);
        $resultBySnapshot = $this->groupResultBySnapshot($result);

        $sumBySnapshot = [];

        dump($resultBySnapshot);

        foreach ($resultBySnapshot as $snapshotId => $result) {
            $total = null;
            foreach ($result as $row) {
                if ($row['adj_close'] != null and $row['date'] != null) {
                    $total += (float)$row['adj_close'] * (int)$row['amount'];
                }
            }

            $sumBySnapshot[$snapshotId] = [
                'date' => $result[0]['date'],
                'total' => $total
            ];
        }

        dump($sumBySnapshot);

//        dump($result);

    }


    private function groupResultBySnapshot(array $result)
    {
        $bySnapshot = [];
        $bySnapshot[$result[0]['snapshot_id']][] = $result[0];

        for ($i = 1; $i < count($result); $i++) {
            $bySnapshot[$result[$i]['snapshot_id']][] = $result[$i];
        }

        return $bySnapshot;
    }

    private function fillNullDatesAndCosts(array $graphData, Portfolio $portfolio, \DateTime $start, \DateTime $end)
    {
        $quotesInUse = $this->getQuotesInUse($portfolio, $start, $end);
        $quotesInUseHistoryByQuotes = $this->performQuotesInUseHistoryByQoutes($start, $end, $quotesInUse);

        foreach ($graphData as &$row) {
            if ($row['adj_close'] == null) {

                for ($i = 0; $i < count($quotesInUseHistoryByQuotes[$row['quote_id']]) - 1; $i++) {

                    if (
                        $quotesInUseHistoryByQuotes[$row['quote_id']][$i]['date'] > $row['moment'] and
                        $quotesInUseHistoryByQuotes[$row['quote_id']][$i + 1]['date'] < $row['moment']
                    ) {
                        $row['adj_close'] = $quotesInUseHistoryByQuotes[$row['quote_id']][$i + 1]['adj_close'];
                        $row['date'] = $quotesInUseHistoryByQuotes[$row['quote_id']][$i + 1]['date'];
                        break;
                    }
                }
            }
        }

        return $graphData;
    }

    /**
     * @param Portfolio $portfolio
     * @param \DateTime $start
     * @param \DateTime $end
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getQuotesInUse(Portfolio $portfolio, \DateTime $start, \DateTime $end)
    {
        $quotesInUseStatement = $this->em->getConnection()->prepare("
            SELECT DISTINCT (quote_id)
            FROM portfolio_snapshot AS Snapshot
            WHERE Snapshot.portfolio_id = :portfolio_id
                AND DATE(Snapshot.moment) >= :startDate
                AND DATE(Snapshot.moment) <= :endDate
        ");

        $params = [
            'portfolio_id' => $portfolio->getId(),
            'startDate' => $start->format('Y-m-d'),
            'endDate' => $end->format('Y-m-d'),
        ];

        $quotesInUseStatement->execute($params);
        $quotesInUse = $quotesInUseStatement->fetchAll();
        $quotesInUse = array_map(function ($el) {
            return $el['quote_id'];
        }, $quotesInUse);

        return $quotesInUse;
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @param $quotesInUse
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function performQuotesInUseHistoryByQoutes(\DateTime $start, \DateTime $end, $quotesInUse)
    {
        $quoteInUseHistoryStatement = $this->em->getConnection()->prepare("
            SELECT quote_id, date, adj_close
            FROM quote_char AS Quotes
            WHERE Quotes.quote_id IN (" . implode(',', $quotesInUse) . ")
                  AND Quotes.date >= DATE(:startDate) - INTERVAL 3 DAY
                  AND Quotes.date <= DATE(:endDate) + INTERVAL 3 DAY
            ORDER BY Quotes.date DESC
        ");

        $params = [
            'startDate' => $start->format('Y-m-d'),
            'endDate' => $end->format('Y-m-d'),
        ];

        $quoteInUseHistoryStatement->setFetchMode(\PDO::FETCH_ASSOC);
        $quoteInUseHistoryStatement->execute($params);
        $quoteInUseHistory = $quoteInUseHistoryStatement->fetchAll();


        $quoteInUseHistoryByQuotes = [];

        foreach ($quotesInUse as $quote) {
            $quoteInUseHistoryByQuotes[$quote] = [];
        }

        foreach ($quoteInUseHistory as $row) {
            foreach ($quotesInUse as $quote) {
                if ($row['quote_id'] == $quote) {
                    $quoteInUseHistoryByQuotes[$quote][] = $row;
                }
            }
        }

        return $quoteInUseHistoryByQuotes;
    }
}