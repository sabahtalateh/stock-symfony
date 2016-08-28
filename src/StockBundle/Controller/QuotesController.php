<?php

namespace StockBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use StockBundle\Core\YahooFinanceApi;
use StockBundle\Entity\Portfolio;
use StockBundle\Form\PortfolioType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;

class QuotesController extends Controller
{
    public function indexAction(Request $request)
    {
        if ($this->needToCreateOrActivatePortfolio()) {
            $this->addFlash(
                'notice',
                'Create or activate portfolio before you can use all amazing stuff.'
            );

            return new RedirectResponse($this->generateUrl('portfolio_new'));
        }

        $stockApi = $this->get('stock.core.stock');
        $quotes = $this->getDoctrine()->getRepository('StockBundle:Quote')->getAllSymbols();
        $quotesList = $stockApi->getQuotesList($quotes);


        return $this->render(':cabinet/quotes:index.html.twig', [
            'quotes' => $quotesList,
        ]);
    }

    public function buyAction(Request $request)
    {
        if ($this->needToCreateOrActivatePortfolio()) {
            $this->addFlash(
                'notice',
                'Create or activate portfolio before you can use all amazing stuff.'
            );

            return new RedirectResponse($this->generateUrl('portfolio_new'));
        }

        $csrfManager = $this->get('security.csrf.token_manager');
        $csrf = new CsrfToken('quotes_buy', $request->get('_csrf_token'));

        if (!$csrfManager->isTokenValid($csrf)) {
            throw new \Exception('CSRF is not valid');
        }

        $user = $this->getUser();

        /** @var Portfolio $portfolio */
        $portfolio = $this
            ->getDoctrine()
            ->getRepository('StockBundle:Portfolio')
            ->getActivePortfolioForUser($user);

        $quote = $this
            ->getDoctrine()
            ->getRepository('StockBundle:Quote')
            ->findOneBy([
                'symbol' => $request->get('symbol')
            ]);

        $amount = $request->get('amount');

        $this
            ->getDoctrine()
            ->getRepository('StockBundle:Portfolio')
            ->addQuote($portfolio, $quote, $amount, new \DateTime());

        return new RedirectResponse($this->generateUrl('portfolio_show_quotes', ['id' => $portfolio->getId()]));
    }

    private function needToCreateOrActivatePortfolio()
    {
        $user = $this->getUser();

        $needToCreateOrActivatePortfolio = false;
        if (!$user->getPortfolio()->first()) {
            $needToCreateOrActivatePortfolio = true;
        } else {
            $hasActivePortfolio = false;

            /** @var Portfolio $portfolio */
            foreach ($user->getPortfolio() as $portfolio) {
                if ($portfolio->getActive()) {
                    $hasActivePortfolio = true;
                }
            }

            if (!$hasActivePortfolio) $needToCreateOrActivatePortfolio = true;
        }

        return $needToCreateOrActivatePortfolio;
    }
}
