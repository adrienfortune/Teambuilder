<?php

namespace Tm\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
* Utilisateur
*
* @ORM\Entity
* @ORM\Table(name="utilisateur")
*/
class Utilisateur extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Tm\TeambuilderBundle\Entity\Equipe
     * @ORM\OneToOne(targetEntity="Tm\TeambuilderBundle\Entity\Equipe", mappedBy="utilisateur", cascade={"persist", "remove"})
     */
    private $equipe;

    /**
     * Set equipe
     *
     * @param \Tm\TeambuilderBundle\Entity\Equipe $equipe
     * @return \Tm\TeambuilderBundle\Entity\Equipe
     */
    public function setEquipe(\Tm\TeambuilderBundle\Entity\Equipe $equipe)
    {
        $this->equipe = $equipe;

        return $this;
    }

    /**
     * Get equipe
     *
     * @return \Tm\TeambuilderBundle\Entity\Equipe
     */
    public function getEquipe()
    {
        return $this->equipe;
    }

    public function addRole($role)
    {
        $role = strtoupper($role);

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }


    public function getId() {
        return $this->id;
    }
}
