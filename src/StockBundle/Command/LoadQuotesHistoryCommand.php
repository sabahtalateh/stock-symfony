<?php

namespace StockBundle\Command;

use Doctrine\ORM\Query\Expr\OrderBy;
use StockBundle\Core\Stock;
use StockBundle\Entity\Quote;
use StockBundle\Entity\QuoteChar;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadQuotesHistoryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stock:load-history')
            ->setDescription('Loads stock quotes history');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $isQuoteCharsEmpty = count($this
                ->getContainer()
                ->get('doctrine')
                ->getRepository('StockBundle:QuoteChar')
                ->findAll()) == 0;

        for ($crashes = 0; $crashes < 3; $crashes++) {
            try {
                if (!$isQuoteCharsEmpty) {
                    $this->updateHistory();
                    $output->writeln('History quotes updated.');
                } else {
                    $this->firstLoadHistory();
                    $output->writeln('First loading completed.');
                }
                return;
            } catch (\Exception $e) {
                $output->writeln('Retrying. Try #' . $crashes . ' is failed.');
                if ($crashes < 2) {
                    sleep(1);
                } else {
                    $output->writeln('Error. Exit. Try to decrease period of updating or increase timeout in stock service configuration.');
                }
            }
        }

    }

    private function updateHistory()
    {
        /** @var QuoteChar $lastUpdatedChar */
        $lastUpdatedChar = $this
            ->getContainer()
            ->get('doctrine')
            ->getRepository('StockBundle:QuoteChar')
            ->createQueryBuilder('quoteChar')
            ->where('quoteChar.date IS NOT NULL')
            ->orderBy(new OrderBy('quoteChar.date', 'DESC'))
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()[0];

        $lastUpdatedDate = $lastUpdatedChar->getDate();
        $todayMidnight = (new \DateTime())->setTimestamp(strtotime('today midnight'));

        $lastUpdatedDaysAgo = date_diff($todayMidnight, $lastUpdatedDate)->days;

        if ($lastUpdatedDaysAgo > 1) {
            $quotes = $this->getContainer()->get('doctrine')->getRepository('StockBundle:Quote')->findAll();
            $stock = $this->getContainer()->get('stock.core.stock');

            $lastUpdatedDate = $lastUpdatedDate->modify('+1 day');

            array_map(function ($quote) use ($stock, $lastUpdatedDate, $todayMidnight) {
                /** @var Quote $quote */
                $historicalChars = $stock->getHistoricalQuotes(
                    $quote->getSymbol(),
                    $lastUpdatedDate,
                    $todayMidnight
                );

                $this->insertIntoChars($quote, $historicalChars);

            }, $quotes);
        }

    }

    private function firstLoadHistory()
    {
        $quotes = $this->getContainer()->get('doctrine')->getRepository('StockBundle:Quote')->findAll();
        $stock = $this->getContainer()->get('stock.core.stock');

        array_map(function ($quote) use ($stock) {
            /** @var Quote $quote */
            $oneYearAgoQuotes = $stock->getHistoricalQuotes(
                $quote->getSymbol(),
                (new \DateTime())->modify('-1 year'),
                new \DateTime()
            );
            $this->insertIntoChars($quote, $oneYearAgoQuotes);

            $twoYearsAgoQuotes = $stock->getHistoricalQuotes(
                $quote->getSymbol(),
                (new \DateTime())->modify('-2 year'),
                (new \DateTime())->modify('-1 year')
            );
            $this->insertIntoChars($quote, $twoYearsAgoQuotes);

        }, $quotes);
    }

    private function insertIntoChars(Quote $quote, $chars)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        // Fix if there is just one quote been given
        if ($chars['count'] == 1) {
            $tmp = $chars['results']['quote'];
            unset($chars['results']['quote']);
            $chars['results']['quote'][] = $tmp;
        }

        foreach ($chars['results']['quote'] as $row) {
            $quoteChar = new QuoteChar();
            $quoteChar->setQuote($quote);
            $quoteChar->setDate(new \DateTime($row['Date']));
            $quoteChar->setOpenPrice($row['Open']);
            $quoteChar->setClosePrice($row['Close']);
            $quoteChar->setDaysHigh($row['High']);
            $quoteChar->setDaysLow($row['Low']);
            $quoteChar->setVolume($row['Volume']);
            $quoteChar->setAdjClose($row['Adj_Close']);

            $em->persist($quoteChar);
        }

        $em->flush();
    }
}
