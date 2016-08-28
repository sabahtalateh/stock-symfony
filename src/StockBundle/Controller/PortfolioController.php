<?php

namespace StockBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use StockBundle\Entity\Portfolio;
use StockBundle\Form\PortfolioType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Portfolio controller.
 *
 */
class PortfolioController extends Controller
{
    /**
     * Lists all Portfolio entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $portfolios = $em->getRepository('StockBundle:Portfolio')->findBy(['user' => $this->getUser()]);

        return $this->render('cabinet/portfolio/index.html.twig', array(
            'portfolios' => $portfolios,
        ));
    }

    /**
     * Creates a new Portfolio entity.
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $portfolio = new Portfolio();
        $portfolio->setUser($this->getUser());
        $form = $this->createForm('StockBundle\Form\PortfolioType', $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($portfolio->getActive()) {
                $portfolios = $this
                    ->getDoctrine()
                    ->getRepository('StockBundle:Portfolio')
                    ->findBy(['user' => $this->getUser()]);

                /** @var Portfolio $p */
                foreach ($portfolios as $p) {
                    $p->setActive(false);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolio);
            $em->flush();

            return $this->redirectToRoute('portfolio_index', array('id' => $portfolio->getId()));
        }

        return $this->render('cabinet/portfolio/new.html.twig', array(
            'portfolio' => $portfolio,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Portfolio entity.
     * @param Portfolio $portfolio
     * @return Response
     */
    public function showAction(Portfolio $portfolio)
    {
        $deleteForm = $this->createDeleteForm($portfolio);

        return $this->render('cabinet/portfolio/show.html.twig', array(
            'portfolio' => $portfolio,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Portfolio entity.
     * @param Request $request
     * @param Portfolio $portfolio
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Portfolio $portfolio)
    {
        $deleteForm = $this->createDeleteForm($portfolio);
        $editForm = $this->createForm('StockBundle\Form\PortfolioType', $portfolio);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            if ($portfolio->getActive()) {
                $portfolios = $this
                    ->getDoctrine()
                    ->getRepository('StockBundle:Portfolio')
                    ->findBy(['user' => $this->getUser()]);

                /** @var Portfolio $p */
                foreach ($portfolios as $p) {
                    $p->setActive(false);
                }

                $portfolio->setActive(true);
            }


            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolio);
            $em->flush();

            return $this->redirectToRoute('portfolio_index', array('id' => $portfolio->getId()));
        }

        return $this->render('cabinet/portfolio/edit.html.twig', array(
            'portfolio' => $portfolio,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Portfolio entity.
     * @param Request $request
     * @param Portfolio $portfolio
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Portfolio $portfolio)
    {
        $form = $this->createDeleteForm($portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($portfolio);
            $em->flush();
        }

        return $this->redirectToRoute('portfolio_index');
    }

    public function quotesShowAction(Portfolio $portfolio)
    {
        return $this->render(':cabinet/portfolio:show_quotes.html.twig', ['portfolio' => $portfolio]);
    }

    public function activateAction(Portfolio $portfolio)
    {
        $this
            ->getDoctrine()
            ->getRepository('StockBundle:Portfolio')
            ->activatePortfolio($portfolio);


        return new RedirectResponse($this->generateUrl('portfolio_index'));
    }

    public function graphAction(Portfolio $portfolio)
    {
        $startDate = (new \DateTime())->modify('-2 year');
        $endDate = new \DateTime();

        $graph = $this->get('stock.core.stock_graph_builder')->build($portfolio, $startDate);

        return $this->render(':cabinet/portfolio:graph.html.twig', [
            'graph' => $graph,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }

    /**
     * Creates a form to delete a Portfolio entity.
     *
     * @param Portfolio $portfolio The Portfolio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Portfolio $portfolio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('portfolio_delete', array('id' => $portfolio->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
