<?php

namespace StockBundle\Command;

use StockBundle\Core\Stock;
use StockBundle\Entity\Portfolio;
use StockBundle\Entity\Quote;
use StockBundle\Entity\QuoteChar;
use StockBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddRandomQuotesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stock:random-quotes')
            ->setDescription('Add random quotes for 6 months')
            ->addArgument('username', InputArgument::REQUIRED, 'Username to add quotes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');

        $quotes = $doctrine
            ->getRepository('StockBundle:Quote')
            ->findAll();

        $username = $input->getArgument('username');

        /** @var User $user */
        $user = $doctrine
            ->getRepository('StockBundle:User')
            ->findOneBy(['username' => $username]);

        if (!$user) {
            throw new \Exception('No such user');
        }

        $begin = (new \DateTime())->modify('-6 months');
        $end = new \DateTime();

        $interval = \DateInterval::createFromDateString('666 minutes');
        $period = new \DatePeriod($begin, $interval, $end);

        $faker = $this->getContainer()->get('faker.factory')->create();

        /** @var Portfolio $portfolio */
        $portfolio = $doctrine->getRepository('StockBundle:Portfolio')
            ->getActivePortfolioForUser($user);

        if ($portfolio == null) {
            $output->writeln('This user has no active portfolio to add quotes. Please create one.');
            return;
        }

        foreach ($period as $date) {
            $doctrine
                ->getRepository('StockBundle:Portfolio')
                ->addQuote($portfolio, $faker->randomElement($quotes), $faker->numberBetween(1, 1000), $date);

            

        }

        $output->writeln('Random quotes added');
    }
}
