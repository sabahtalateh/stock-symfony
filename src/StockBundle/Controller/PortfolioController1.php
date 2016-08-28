<?php

namespace StockBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use StockBundle\Core\YahooFinanceApi;
use StockBundle\Entity\Portfolio;
use StockBundle\Form\PortfolioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PortfolioController1 extends Controller
{
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $portfolio = $this->getDoctrine()->getRepository('StockBundle:Portfolio')->findBy(['user' => $user]);

        return $this->render(':cabinet/portfolio:index.html.twig', [
            'portfolio' => $portfolio
        ]);
    }

    public function createAction()
    {
        return new Response("<html><body>456</body></html>");
    }

    public function graphAction()
    {
        $user = $this->getUser();

        $startDate = (new \DateTime())->modify('-1 month');

        $snapshots = $this
            ->getDoctrine()
            ->getRepository('StockBundle:PortfolioSnapshot')
            ->createQueryBuilder('portfolioSnapshot')
            ->where('portfolioSnapshot.user = :user')
            ->setParameter('user', $user)
            ->andWhere('portfolioSnapshot.datetime >= :startDate')
            ->setParameter('startDate', $startDate)
            ->getQuery()
            ->getResult();

        $quoteChars = $this
            ->getDoctrine()
            ->getManager()
            ->createQuery("
                SELECT qc, q FROM StockBundle:Quote q
                JOIN StockBundle:QuoteChar qc
                WHERE q.id = qc.quote
                
            ");


        dump($quoteChars->getResult());


        return $this->render(':cabinet/portfolio:graph.html.twig');
    }

    public function editAction($quoteId)
    {
        return new Response("<html><body>123</body></html>");
    }
}
