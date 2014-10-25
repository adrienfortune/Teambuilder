<?php

namespace Tm\TeambuilderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Equipe
 *
 * @ORM\Table(name="equipe")
 * @ORM\Entity(repositoryClass="Tm\TeambuilderBundle\Entity\EquipeRepository")
 */
class Equipe
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="LIBELLE", type="string", length=64, nullable=false)
     */
    private $libelle;

    /**
     * @var boolean
     *
     * @ORM\Column(name="IS_PUBLIC", type="boolean", nullable=false)
     */
    private $isPublic = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_CREATION", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @var \Tm\UserBundle\Entity\Utilisateur
     * @ORM\OneToOne(targetEntity="Tm\UserBundle\Entity\Utilisateur", inversedBy="equipe")
     * @ORM\JoinColumn(name="ID_UTILISATEUR", referencedColumnName="id")
     */
    private $utilisateur;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     * @return Equipe
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set isPublic
     *
     * @param boolean $isPublic
     * @return Equipe
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic
     *
     * @return boolean 
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Equipe
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set utilisateur
     *
     * @param \Tm\UserBundle\Entity\Utilisateur $utilisateur
     * @return \Tm\UserBundle\Entity\Utilisateur Utilisateur
     */
    public function setUtilisateur(\Tm\UserBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \Tm\UserBundle\Entity\Utilisateur $utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

}
