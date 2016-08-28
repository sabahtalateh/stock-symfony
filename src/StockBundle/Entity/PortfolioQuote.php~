<?php

namespace StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * PortfolioQuote
 *
 * @ORM\Table(
 *     name="portfolio_quote",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="u_portfolio_quote", columns={"portfolio_id", "quote_id"})}
 * )
 * @ORM\Entity(repositoryClass="StockBundle\Repository\PortfolioQuoteRepository")
 * @UniqueEntity(
 *     fields={"portfolio","quote"}
 * )
 *
 */
class PortfolioQuote
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    
    /**
     * @var Portfolio
     *
     * @ORM\ManyToOne(targetEntity="StockBundle\Entity\Portfolio", inversedBy="portfolioQuotes")
     * @ORM\JoinColumn(name="portfolio_id", referencedColumnName="id")
     */
    protected $portfolio;

    
    /**
     * @var Quote
     *
     * @ORM\ManyToOne(targetEntity="StockBundle\Entity\Quote", inversedBy="portfolioQuotes")
     * @ORM\JoinColumn(name="quote_id", referencedColumnName="id")
     */
    protected $quote;


    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer", length=11)
     */
    protected $amount;

    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return PortfolioQuote
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set portfolio
     *
     * @param \StockBundle\Entity\Portfolio $portfolio
     *
     * @return PortfolioQuote
     */
    public function setPortfolio(\StockBundle\Entity\Portfolio $portfolio = null)
    {
        $this->portfolio = $portfolio;

        return $this;
    }

    /**
     * Get portfolio
     *
     * @return \StockBundle\Entity\Portfolio
     */
    public function getPortfolio()
    {
        return $this->portfolio;
    }

    /**
     * Set quote
     *
     * @param \StockBundle\Entity\Quote $quote
     *
     * @return PortfolioQuote
     */
    public function setQuote(\StockBundle\Entity\Quote $quote = null)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote
     *
     * @return \StockBundle\Entity\Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }
}
