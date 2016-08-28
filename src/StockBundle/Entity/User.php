<?php

namespace StockBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use StockBundle\Entity\Portfolio;
use StockBundle\Entity\PortfolioSnapshot;
use Symfony\Component\Validator\Constraints;

use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="StockBundle\Repository\UserRepository")
 */
class User extends BaseUser
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
     * @ORM\OneToMany(targetEntity="StockBundle\Entity\Portfolio", mappedBy="user")
     */
    protected $portfolio;

    /**
     * Add portfolio
     *
     * @param \StockBundle\Entity\Portfolio $portfolio
     *
     * @return User
     */
    public function addPortfolio(\StockBundle\Entity\Portfolio $portfolio)
    {
        $this->portfolio[] = $portfolio;

        return $this;
    }

    /**
     * Remove portfolio
     *
     * @param \StockBundle\Entity\Portfolio $portfolio
     */
    public function removePortfolio(\StockBundle\Entity\Portfolio $portfolio)
    {
        $this->portfolio->removeElement($portfolio);
    }

    /**
     * Get portfolio
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPortfolio()
    {
        return $this->portfolio;
    }
}
