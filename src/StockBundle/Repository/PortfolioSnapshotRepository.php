<?php

namespace StockBundle\Repository;

use Ramsey\Uuid\Uuid;
use StockBundle\Entity\Portfolio;
use StockBundle\Entity\PortfolioSnapshot;
use StockBundle\Entity\Quote;
use StockBundle\Entity\QuoteChar;

/**
 * PortfolioSnapshotRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PortfolioSnapshotRepository extends \Doctrine\ORM\EntityRepository
{
    public function makeSnapshot(Portfolio $portfolio, Quote $quote, $amount, \DateTime $moment)
    {
        $em = $this->getEntityManager();

        /** @var PortfolioSnapshot[] $previousSnapshot */
        $previousSnapshot = $this
            ->getEntityManager()
            ->createQuery("
                SELECT snapshot
                FROM StockBundle:PortfolioSnapshot snapshot
                WHERE snapshot.portfolio = :portfolio
                  GROUP BY snapshot.id
                HAVING snapshot.moment = MAX(snapshot.moment)
                  ORDER BY snapshot.moment DESC
            ")
            ->setParameter('portfolio', $portfolio)
            ->setMaxResults(1)
            ->getResult();

        // If there is no previous snapshots, creates a brand new one
        if (count($previousSnapshot) == 0) {

            $portfolioSnapshot = new PortfolioSnapshot();
            $portfolioSnapshot->setSnapshot(Uuid::uuid4());
            $portfolioSnapshot->setQuote($quote);
            $portfolioSnapshot->setPortfolio($portfolio);
            $portfolioSnapshot->setAmount($amount);
            $portfolioSnapshot->setMoment($moment);

            $em->persist($portfolioSnapshot);

        } else {
            // Else we must copy previous snapshot and add currently added quote
            $previousSnapshot = $previousSnapshot[0]->getSnapshot();

            $previousSnapshots = $this
                ->findBy(['snapshot' => $previousSnapshot]);

            $newSnapshotUUID = Uuid::uuid4();

            // Flag that indicates if currently added quote been updated
            // if it false we must add it to snapshot
            $quoteUpdated = false;

            /** @var PortfolioSnapshot $snapshot */
            foreach ($previousSnapshots as $snapshot) {

                $newSnapshot = new PortfolioSnapshot();
                $newSnapshot->setPortfolio($snapshot->getPortfolio());
                $newSnapshot->setQuote($snapshot->getQuote());
                $newSnapshot->setAmount($snapshot->getAmount());
                $newSnapshot->setSnapshot($newSnapshotUUID);
                $newSnapshot->setMoment($moment);

                // If currently added quote is already in previous snapshot increase it's amount
                if ($newSnapshot->getQuote() == $quote) {
                    $newSnapshot->setAmount($snapshot->getAmount() + $amount);
                    $quoteUpdated = true;
                }

                $em->persist($newSnapshot);
            }

            if (!$quoteUpdated) {
                $newSnapshot = new PortfolioSnapshot();
                $newSnapshot->setPortfolio($portfolio);
                $newSnapshot->setQuote($quote);
                $newSnapshot->setAmount($amount);
                $newSnapshot->setSnapshot($newSnapshotUUID);
                $newSnapshot->setMoment($moment);

                $em->persist($newSnapshot);
            }
        }

        $em->flush();
    }

    public function buildSnapshotGraph(Portfolio $portfolio, \DateTime $start, \DateTime $end = null)
    {
        if ($end == null) $end = new \DateTime();

        $graphRawData = $this
            ->getEntityManager()
            ->createQuery("
                SELECT snapshot, quoteChar
                FROM StockBundle:PortfolioSnapshot snapshot
                LEFT JOIN StockBundle:QuoteChar quoteChar
                WHERE snapshot.quote = quoteChar.quote
                AND DATE(snapshot.moment) = quoteChar.date
                AND DATE(snapshot.moment) >= :startDate
                AND DATE(snapshot.moment) <= :endDate
                AND snapshot.portfolio = :portfolio
                ORDER BY snapshot.moment DESC
            ")
            ->setParameter('startDate', $start)
            ->setParameter('endDate', $end)
            ->setParameter('portfolio', $portfolio)
            ->getResult();

//        dump($graphRawData);
//        die();


        if (count($graphRawData) > 0) {
            $graphRawData = $this->fixRawGraphData($graphRawData);

            $costPerDate = [];

            // If after fixing raw data still no first quote characteristic
            if ($graphRawData[1] == null) {
                return null;
            }

            /** @var PortfolioSnapshot $firstSnapshot */
            $firstSnapshot = $graphRawData[0];

            /** @var QuoteChar $firstQuoteChar */
            $firstQuoteChar = $graphRawData[1];

            $costPerDate[$firstSnapshot->getSnapshot()] = [
                'moment' => $firstSnapshot->getMoment(),
                'amount' => $firstQuoteChar->getAdjClose() * $firstSnapshot->getAmount()
            ];

            dump($graphRawData);

            for ($i = 2; $i < count($graphRawData) - 2; $i += 2) {

                $snapshotIndex = $i;
                $quoteCharIndex = $i + 1;

                /** @var PortfolioSnapshot $currentSnapshot */
                $currentSnapshot = $graphRawData[$snapshotIndex];
                /** @var QuoteChar $currentQuoteChar */
                $currentQuoteChar = $graphRawData[$quoteCharIndex];

                /** @var PortfolioSnapshot $nextSnapshot */
                $nextSnapshot = $graphRawData[$snapshotIndex + 2];
                /** @var QuoteChar $nextQuoteChar */
                $nextQuoteChar = $graphRawData[$quoteCharIndex + 2];

                $currentSnapshotDate = $currentSnapshot->getMoment();
                $nextSnapshotDateDate = $nextSnapshot->getMoment();


                if ($currentSnapshot->getSnapshot() == $nextSnapshot->getSnapshot()) {
//                if ($currentSnapshotDate == $nextSnapshotDateDate) {

//                    $costPerDate[$nextSnapshot->getSnapshot()]['moment'] = $nextSnapshot->getMoment();
                    $costPerDate[$nextSnapshot->getSnapshot()]['amount'] =
                        $nextQuoteChar->getAdjClose() * $nextSnapshot->getAmount() +
                        $costPerDate[$nextSnapshot->getSnapshot()]['amount'];

//                    $costPerDate[$nextSnapshotDateDate->format('d-m-Y')] =
//                        $nextQuoteChar->getAdjClose() * $nextSnapshot->getAmount() +
//                        $costPerDate[$nextSnapshotDateDate->format('d-m-Y')];

//                    $totalCostPerDate = $nextQuoteChar->getAdjClose() * $nextSnapshot->getAmount() + $totalCostPerDate;
                } else {

                    $costPerDate[$nextSnapshot->getSnapshot()]['moment'] = $nextSnapshot->getMoment();
                    $costPerDate[$nextSnapshot->getSnapshot()]['amount'] = $nextQuoteChar->getAdjClose() * $nextSnapshot->getAmount();

//                    $costPerDate[$nextSnapshotDateDate->format('d-m-Y')] =
//                        $nextQuoteChar->getAdjClose() * $nextSnapshot->getAmount();

//                    dump($totalCostPerDate);
//                    dump($currentSnapshotDate);
                }


            }

            dump($costPerDate);

        }
    }

    private function firstNotNullChar($graphRawData, $startIndex)
    {
        for ($i = $startIndex; $i < count($graphRawData); $i += 2) {
            if (!array_key_exists($i, $graphRawData)) {
                return null;
            }

            if (($graphRawData[$i] != null) and ($graphRawData[$i] instanceof QuoteChar)) {
                return $graphRawData[$i];
            }
        }

        return null;
    }

    /**
     * @param $graphRawData
     *
     * @return array
     */
    private function fixRawGraphData(&$graphRawData)
    {
        if (count($graphRawData) % 2 != 0) {
            $graphRawData[count($graphRawData)] = $graphRawData[count($graphRawData) - 2];
        }

        // Fill empty historical data - holidays
        for ($i = 0; $i < count($graphRawData); $i += 2) {
            $quoteCharIndex = $i + 1;

            $quoteChar = $graphRawData[$quoteCharIndex];

            if ($quoteChar == null) {
                $quoteChar = $this->firstNotNullChar($graphRawData, $quoteCharIndex);
            }

            $graphRawData[$quoteCharIndex] = $quoteChar;
        }

        return $graphRawData;
    }
}
