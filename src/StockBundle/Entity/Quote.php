<?php

namespace StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Quote
 *
 * @ORM\Table(name="quote")
 * @ORM\Entity(repositoryClass="StockBundle\Repository\QuoteRepository")
 */
class Quote
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
     * @var string
     *
     * @ORM\Column(name="symbol", type="string", length=100)
     */
    protected $symbol;

    /**
     * @var QuoteChar
     *
     * @ORM\OneToMany(targetEntity="StockBundle\Entity\QuoteChar", mappedBy="quote")
     */
    protected $chars;


    /**
     * @var PortfolioQuote
     *
     * @ORM\OneToMany(targetEntity="StockBundle\Entity\PortfolioQuote", mappedBy="quote")
     */
    protected $portfolioQuotes;

    /**
     * @var PortfolioSnapshot
     *
     * @ORM\OneToMany(targetEntity="StockBundle\Entity\PortfolioSnapshot", mappedBy="quote")
     */
    protected $portfolioSnapshot;

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
     * Set symbol
     *
     * @param string $symbol
     *
     * @return Quote
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * Get symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->chars = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add char
     *
     * @param \StockBundle\Entity\QuoteChar $char
     *
     * @return Quote
     */
    public function addChar(\StockBundle\Entity\QuoteChar $char)
    {
        $this->chars[] = $char;

        return $this;
    }

    /**
     * Remove char
     *
     * @param \StockBundle\Entity\QuoteChar $char
     */
    public function removeChar(\StockBundle\Entity\QuoteChar $char)
    {
        $this->chars->removeElement($char);
    }

    /**
     * Get chars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChars()
    {
        return $this->chars;
    }

    /**
     * Add portfolioQuote
     *
     * @param \StockBundle\Entity\PortfolioQuote $portfolioQuote
     *
     * @return Quote
     */
    public function addPortfolioQuote(\StockBundle\Entity\PortfolioQuote $portfolioQuote)
    {
        $this->portfolioQuotes[] = $portfolioQuote;

        return $this;
    }

    /**
     * Remove portfolioQuote
     *
     * @param \StockBundle\Entity\PortfolioQuote $portfolioQuote
     */
    public function removePortfolioQuote(\StockBundle\Entity\PortfolioQuote $portfolioQuote)
    {
        $this->portfolioQuotes->removeElement($portfolioQuote);
    }

    /**
     * Get portfolioQuotes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPortfolioQuotes()
    {
        return $this->portfolioQuotes;
    }

    /**
     * Add portfolioSnapshot
     *
     * @param \StockBundle\Entity\PortfolioSnapshot $portfolioSnapshot
     *
     * @return Quote
     */
    public function addPortfolioSnapshot(\StockBundle\Entity\PortfolioSnapshot $portfolioSnapshot)
    {
        $this->portfolioSnapshot[] = $portfolioSnapshot;

        return $this;
    }

    /**
     * Remove portfolioSnapshot
     *
     * @param \StockBundle\Entity\PortfolioSnapshot $portfolioSnapshot
     */
    public function removePortfolioSnapshot(\StockBundle\Entity\PortfolioSnapshot $portfolioSnapshot)
    {
        $this->portfolioSnapshot->removeElement($portfolioSnapshot);
    }

    /**
     * Get portfolioSnapshot
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPortfolioSnapshot()
    {
        return $this->portfolioSnapshot;
    }
}
