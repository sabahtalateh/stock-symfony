<?php

namespace StockBundle\Core;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use StockBundle\Entity\Portfolio;
use StockBundle\Entity\PortfolioSnapshot;
use StockBundle\Entity\QuoteChar;

class StockGraphBuilder
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Portfolio $portfolio
     * @param \DateTime $start
     * @param \DateTime|null $end
     * @return array
     */
    public function build(Portfolio $portfolio, \DateTime $start, \DateTime $end = null)
    {
        if ($end == null) $end = new \DateTime();

        $quoteCharsGroupedByDate = $this->getGroupedByDateQuoteChars($portfolio, $start, $end);
        $snapshotGroupedByDate = $this->getGroupedByDateSnapshots($portfolio, $start, $end);

        $currentSnapshot = null;

        if (array_keys($snapshotGroupedByDate)[0] <= $start->format('Y-m-d')) {
            $currentSnapshot = $snapshotGroupedByDate[array_keys($snapshotGroupedByDate)[0]];
        }

        $graph = [];

        foreach ($quoteCharsGroupedByDate as $date => $quoteChar) {

            $total = 0;

            if ($currentSnapshot != null) {
                foreach ($currentSnapshot as $quote) {
                    $quoteAmount = $quote['amount'];
                    $quoteId = $quote['quote_id'];
                    $total += $quoteCharsGroupedByDate[$date][$quoteId]['adj_close'] * $quoteAmount;
                }
            }

            $graph[$date] = $total;

            if ($date) {
                if (array_key_exists($date, $snapshotGroupedByDate)) {
                    $currentSnapshot = $snapshotGroupedByDate[$date];
                }
            }
        }

        return $graph;

    }

    private function getGroupedByDateSnapshots(Portfolio $portfolio, \DateTime $start, \DateTime $end)
    {
        // Snapshots with 1 previous
        $snapshotsStatement = $this->em->getConnection()->prepare("
            (SELECT
               Snapshot.snapshot_id,
               Snapshot.quote_id,
               Snapshot.amount,
               Snapshot.moment
             FROM portfolio_snapshot AS Snapshot
               INNER JOIN
               (SELECT Snapshot.snapshot_id
                FROM portfolio_snapshot AS Snapshot
                WHERE Snapshot.portfolio_id = :portfolioId
                      AND Snapshot.moment <= DATE(:startDate)
                ORDER BY moment DESC
                LIMIT 1) AS t
                 ON t.snapshot_id = Snapshot.snapshot_id
            )
            UNION
            (SELECT
               Snapshot.snapshot_id,
               Snapshot.quote_id,
               Snapshot.amount,
               Snapshot.moment
             FROM portfolio_snapshot AS Snapshot
             WHERE Snapshot.portfolio_id = :portfolioId
                   AND Snapshot.moment >= DATE(:startDate)
                   AND Snapshot.moment <= DATE(:endDate)
            )
        ");

        $params = [
            'portfolioId' => $portfolio->getId(),
            'startDate' => $start->format('Y-m-d'),
            'endDate' => $end->format('Y-m-d'),
        ];

        $snapshotsStatement->setFetchMode(\PDO::FETCH_ASSOC);
        $snapshotsStatement->execute($params);
        $snapshots = $snapshotsStatement->fetchAll();

        $snapshotsGroupedByDate = [];

        foreach ($snapshots as $snapshot) {
            $snapshotDate = explode(' ', $snapshot['moment'])[0];
            $snapshotsGroupedByDate[$snapshotDate][$snapshot['moment']][] = $snapshot;
        }

        $onlyLastSnapshotsOnEveryDayGroupedByDate = [];

        // Leave last snapshot on each day
        foreach ($snapshotsGroupedByDate as $key => $item) {
            $lastDatePerDay = max(array_keys($item));
            $onlyLastSnapshotsOnEveryDayGroupedByDate[$key] = $item[$lastDatePerDay];
        }

        return $onlyLastSnapshotsOnEveryDayGroupedByDate;
    }

    private
    function getGroupedByDateQuoteChars(Portfolio $portfolio, \DateTime $start, \DateTime $end)
    {
        $quotesInUse = $this->getQuotesInUse($portfolio);

        $quoteCharsStatement = $this->em->getConnection()->prepare("
            SELECT
              quote_id,
              date,
              adj_close
            FROM quote_char AS Quotes
            WHERE Quotes.quote_id IN (" . implode(',', $quotesInUse) . ")
                  AND Quotes.date >= DATE(:startDate) - INTERVAL 3 DAY
                  AND Quotes.date <= DATE(:endDate) + INTERVAL 3 DAY
        ");

        $params = [
            'startDate' => $start->format('Y-m-d'),
            'endDate' => $end->format('Y-m-d'),
        ];

        $quoteCharsStatement->setFetchMode(\PDO::FETCH_ASSOC);
        $quoteCharsStatement->execute($params);
        $quoteChars = $quoteCharsStatement->fetchAll();

        $quoteCharsByDate = [];

        foreach ($quoteChars as $char) {
            $quoteCharsByDate[$char['date']][$char['quote_id']]
                = $char;
        }

        $newQuoteCharsByDate = [];

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));

        /** @var \DateTime $dt */
        foreach ($period as $dt) {
            $date = $dt->format("Y-m-d");
            if (!array_key_exists($date, $quoteCharsByDate)) {
                $quoteCharsByDateKeys = array_keys($quoteCharsByDate);
                sort($quoteCharsByDateKeys);

                for ($i = 0; $i < count($quoteCharsByDateKeys) - 1; $i++) {
                    if ($quoteCharsByDateKeys[$i] < $date and $date < $quoteCharsByDateKeys[$i + 1]) {
                        $newQuoteCharsByDate[$date] = $quoteCharsByDate[$quoteCharsByDateKeys[$i]];
                        break;
                    } else {
                        $newQuoteCharsByDate[$date] = $quoteCharsByDate[$quoteCharsByDateKeys[count($quoteCharsByDateKeys) - 1]];
                    }
                }
            } else {
                $newQuoteCharsByDate[$date] = $quoteCharsByDate[$date];
            }
        }

        return $newQuoteCharsByDate;
    }

    /**
     * @param Portfolio $portfolio
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    private
    function getQuotesInUse(Portfolio $portfolio)
    {
        $quotesInUseStatement = $this->em->getConnection()->prepare("
            SELECT DISTINCT (quote_id)
            FROM portfolio_snapshot AS Snapshot
            WHERE Snapshot.portfolio_id = :portfolio_id
        ");

        $params = [
            'portfolio_id' => $portfolio->getId()
        ];

        $quotesInUseStatement->execute($params);
        $quotesInUse = $quotesInUseStatement->fetchAll();
        $quotesInUse = array_map(function ($el) {
            return $el['quote_id'];
        }, $quotesInUse);

        return $quotesInUse;
    }
}