<?php

namespace StockBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    public function adminAction()
    {
//        dump($this->get('security.token_storage')->getToken()->getUser());

        $client = new \Scheb\YahooFinanceApi\ApiClient();



//        dump($client->getQuotesList(["YHOO", "GOOG", "FB", "TWTR", "AAPL"]));

//        dump($client->getHistoricalData('YHOO', new \DateTime('2014-01-01 00:00:00'), new \DateTime('2015-01-01 00:00:00')));

        return new Response("<html><body><h1>Admin Page!</h1></body></html>");
    }
}
