<?php

namespace StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Portfolio
 *
 * @ORM\Table(
 *     name="portfolio",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="u_user_portfolio_name", columns={"user_id", "name"})}
 * )
 * @ORM\Entity(repositoryClass="StockBundle\Repository\PortfolioRepository")
 * @UniqueEntity(
 *     fields={"name","user"},
 *     errorPath="name"
 * )
 */
class Portfolio
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="StockBundle\Entity\User", inversedBy="portfolio")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var String
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    protected $active;

    /**
     * @var PortfolioQuote
     *
     * @ORM\OneToMany(targetEntity="StockBundle\Entity\PortfolioQuote", mappedBy="portfolio")
     */
    protected $portfolioQuotes;

    /**
     * @var PortfolioSnapshot
     *
     * @ORM\OneToMany(targetEntity="StockBundle\Entity\PortfolioSnapshot", mappedBy="portfolio")
     */
    protected $snapshot;

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
     * Set name
     *
     * @param string $name
     *
     * @return Portfolio
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set user
     *
     * @param \StockBundle\Entity\User $user
     *
     * @return Portfolio
     */
    public function setUser(\StockBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \StockBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Portfolio
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->portfolioQuotes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add portfolioQuote
     *
     * @param \StockBundle\Entity\PortfolioQuote $portfolioQuote
     *
     * @return Portfolio
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
     * Add snapshot
     *
     * @param \StockBundle\Entity\PortfolioSnapshot $snapshot
     *
     * @return Portfolio
     */
    public function addSnapshot(\StockBundle\Entity\PortfolioSnapshot $snapshot)
    {
        $this->snapshot[] = $snapshot;

        return $this;
    }

    /**
     * Remove snapshot
     *
     * @param \StockBundle\Entity\PortfolioSnapshot $snapshot
     */
    public function removeSnapshot(\StockBundle\Entity\PortfolioSnapshot $snapshot)
    {
        $this->snapshot->removeElement($snapshot);
    }

    /**
     * Get snapshot
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSnapshot()
    {
        return $this->snapshot;
    }
}
