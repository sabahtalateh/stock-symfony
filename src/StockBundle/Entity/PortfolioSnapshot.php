<?php

namespace StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PortfolioSnapshot
 *
 * @ORM\Table(
 *     name="portfolio_snapshot",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="u_snapshot_portfolio_quote", columns={"snapshot_id", "portfolio_id", "quote_id"})}
 * )
 * @ORM\Entity(repositoryClass="StockBundle\Repository\PortfolioSnapshotRepository")
 */
class PortfolioSnapshot
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
     * @ORM\Column(name="snapshot_id", type="string", length=100)
     */
    protected $snapshot;

    /**
     * @var Portfolio
     *
     * @ORM\ManyToOne(targetEntity="StockBundle\Entity\Portfolio", inversedBy="snapshot")
     * @ORM\JoinColumn(name="portfolio_id", referencedColumnName="id")
     */
    protected $portfolio;

    /**
     * @var Quote
     *
     * @ORM\ManyToOne(targetEntity="StockBundle\Entity\Quote", inversedBy="portfolioSnapshot")
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
     * @var \DateTime
     *
     * @ORM\Column(name="moment", type="datetime")
     */
    protected $moment;


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
     * Set snapshot
     *
     * @param string $snapshot
     *
     * @return PortfolioSnapshot
     */
    public function setSnapshot($snapshot)
    {
        $this->snapshot = $snapshot;

        return $this;
    }

    /**
     * Get snapshot
     *
     * @return string
     */
    public function getSnapshot()
    {
        return $this->snapshot;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return PortfolioSnapshot
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
     * Set datetime
     *
     * @param \DateTime $moment
     *
     * @return PortfolioSnapshot
     */
    public function setMoment($moment)
    {
        $this->moment = $moment;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getMoment()
    {
        return $this->moment;
    }

    /**
     * Set portfolio
     *
     * @param \StockBundle\Entity\Portfolio $portfolio
     *
     * @return PortfolioSnapshot
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
     * @return PortfolioSnapshot
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
