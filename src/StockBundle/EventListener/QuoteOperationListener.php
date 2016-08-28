<?php

namespace StockBundle\EventListener;


use StockBundle\Controller\QuotesController;
use StockBundle\Entity\Portfolio;
use StockBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class QuoteOperationListener
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param GetResponseEvent $event
     *
     * Checks if user is logged in and if he has no portfolio than redirects him to create portfolio page
     */
    public function onControllerRequest(GetResponseEvent $event)
    {

//        if ($event->getController()[0] instanceof QuotesController) {
//            if ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getUser()) {
//                /** @var User $user */
//                $user = $this->tokenStorage->getToken()->getUser();
//
//                $route = $event->getRequest()->get('_route');
//                $createPortfolioRoute = $this->router->generate('portfolio_new');
//
//
//                if (is_object($user)) {
//                    $needToCreateOrActivatePortfolio = false;
//                    if (!$user->getPortfolio()->first()) {
//                        $needToCreateOrActivatePortfolio = true;
//                    } else {
//                        $hasActivePortfolio = false;
//
//                        /** @var Portfolio $portfolio */
//                        foreach ($user->getPortfolio() as $portfolio) {
//                            if ($portfolio->getActive()) {
//                                $hasActivePortfolio = true;
//                            }
//                        }
//
//                        if (!$hasActivePortfolio) $needToCreateOrActivatePortfolio = true;
//                    }
//
//                    if ($needToCreateOrActivatePortfolio and $route != 'portfolio_new') {
//                        dump('ertwerewrwq');
////                        header('Location', 'googlr.com');
////                        return new RedirectResponse('/');
//                    }
//
//                }
//            }
//        }
    }

    /**
     * TestListener constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param Router $router
     */
    public function __construct(TokenStorageInterface $tokenStorage, Router $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }
}