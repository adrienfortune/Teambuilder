<?php

namespace Tm\TeambuilderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Regle
 *
 * @ORM\Table(name="regle", indexes={@ORM\Index(name="fk_REGLE_OPERATION1_idx", columns={"ID_OPERATION"}), @ORM\Index(name="fk_REGLE_ROLE1_idx", columns={"ID_ROLE"}), @ORM\Index(name="fk_REGLE_TYPE_ATTAQUE1_idx", columns={"ID_TYPE_ATTAQUE"}), @ORM\Index(name="fk_REGLE_CARACTERISTIQUE1_idx", columns={"ID_CARACTERISTIQUE"}), @ORM\Index(name="fk_REGLE_EQUIPE1_idx", columns={"ID_EQUIPE"})})
 * @ORM\Entity(repositoryClass="Tm\TeambuilderBundle\Entity\RegleRepository")
 */
class Regle
{
    const TYPE_ROLE = 'ROLE';

    const TYPE_TYPE_ATTAQUE = 'TYPE_ATTAQUE';

    const TYPE_CARACTERISTIQUE = 'CARACTERISTIQUE';

    private $count = 0;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="NOMBRE", type="integer", nullable=false)
     */
    private $nombre;

    /**
     * @var integer
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      minMessage = "Pas de nombre négatif ",
     *      maxMessage = "Le nombre ne peut dépasser 5"
     * )
     * @ORM\Column(name="PRIORITE", type="integer", nullable=false)
     */
    private $priorite = 1;

    /**
     * @var \Operation
     *
     * @ORM\ManyToOne(targetEntity="Operation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_OPERATION", referencedColumnName="ID")
     * })
     */
    private $operation;

    /**
     * @var \Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ROLE", referencedColumnName="ID")
     * })
     */
    private $role;

    /**
     * @var \TypeAttaque
     *
     * @ORM\ManyToOne(targetEntity="TypeAttaque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TYPE_ATTAQUE", referencedColumnName="ID")
     * })
     */
    private $typeAttaque;

    /**
     * @var \Caracteristique
     *
     * @ORM\ManyToOne(targetEntity="Caracteristique")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_CARACTERISTIQUE", referencedColumnName="ID")
     * })
     */
    private $caracteristique;

    /**
     * @var \Equipe
     *
     * @ORM\ManyToOne(targetEntity="Equipe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EQUIPE", referencedColumnName="ID")
     * })
     */
    private $equipe;


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
     * Set nombre
     *
     * @param integer $nombre
     * @return Regle
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return integer 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set priorite
     *
     * @param integer $priorite
     * @return Regle
     */
    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * Get priorite
     *
     * @return integer 
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set operation
     *
     * @param \Tm\TeambuilderBundle\Entity\Operation $operation
     * @return Regle
     */
    public function setOperation(\Tm\TeambuilderBundle\Entity\Operation $operation = null)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return \Tm\TeambuilderBundle\Entity\Operation 
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set role
     *
     * @param \Tm\TeambuilderBundle\Entity\Role $role
     * @return Regle
     */
    public function setRole(\Tm\TeambuilderBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Tm\TeambuilderBundle\Entity\Role 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set typeAttaque
     *
     * @param \Tm\TeambuilderBundle\Entity\TypeAttaque $typeAttaque
     * @return Regle
     */
    public function setTypeAttaque(\Tm\TeambuilderBundle\Entity\TypeAttaque $typeAttaque = null)
    {
        $this->typeAttaque = $typeAttaque;

        return $this;
    }

    /**
     * Get typeAttaque
     *
     * @return \Tm\TeambuilderBundle\Entity\TypeAttaque 
     */
    public function getTypeAttaque()
    {
        return $this->typeAttaque;
    }

    /**
     * Set caracteristique
     *
     * @param \Tm\TeambuilderBundle\Entity\Caracteristique $caracteristique
     * @return Regle
     */
    public function setCaracteristique(\Tm\TeambuilderBundle\Entity\Caracteristique $caracteristique = null)
    {
        $this->caracteristique = $caracteristique;

        return $this;
    }

    /**
     * Get caracteristique
     *
     * @return \Tm\TeambuilderBundle\Entity\Caracteristique 
     */
    public function getCaracteristique()
    {
        return $this->caracteristique;
    }

    /**
     * Set equipe
     *
     * @param \Tm\TeambuilderBundle\Entity\Equipe $equipe
     * @return Regle
     */
    public function setEquipe(\Tm\TeambuilderBundle\Entity\Equipe $equipe = null)
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

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if( $this->getCaracteristique() == null && $this->getTypeAttaque()== null)
        {
            $context->addViolationAt(
                'caracteristique',
                'Au moins un des champs: Caractéristique, ou Type d\'attaque doit être renseigné',
                array(),
                null
            );

            $context->addViolationAt(
                'typeAttaque',
                'Au moins un des champs: Caractéristique, ou Type d\'attaque doit être renseigné',
                array(),
                null
            );
        }
    }

    public function getTypeCritere()
    {
        if (null != $this->role) {
            return self::TYPE_ROLE;
        } elseif (null != $this->typeAttaque) {
            return self::TYPE_TYPE_ATTAQUE;
        } elseif (null != $this->caracteristique) {
            return self::TYPE_CARACTERISTIQUE;
        }

        return null;
    }

    public function getCritere()
    {
        switch ($this->getTypeCritere()) {
            case self::TYPE_ROLE:
                return $this->getRole()->getCode();
            case self::TYPE_TYPE_ATTAQUE:
                return $this->getTypeAttaque()->getCode();
            case self::TYPE_CARACTERISTIQUE:
                return $this->getCaracteristique()->getCode();
        }

        return null;
    }

    public function test(Champion $champion, $prisEnCompte )
    {
        $testCritere = false;

        switch ($this->getTypeCritere()) {
            case self::TYPE_ROLE:
                $testCritere = $champion->hasRole($this->getRole());

                break;
            case self::TYPE_TYPE_ATTAQUE:
                $testCritere = $champion->hasTypeAttaque($this->getTypeAttaque());

                break;
            case self::TYPE_CARACTERISTIQUE:
                $testCritere = $champion->hasCaracteristique($this->getCaracteristique());

                break;
        }
        
        if ($prisEnCompte && $testCritere) {
            $this->count++;
        }

        return $testCritere;
    }

    public function testSuggestion(Champion $champion)
    {
        return $this->test($champion, false);
    }

    public function testNombreOccurences()
    {

        switch ($this->operation->getCode()) {
            case Operation::TYPE_AU_MOINS :
                    return ($this->count + 1) >= $this->getNombre();
            case Operation::TYPE_AU_PLUS :
                return ($this->count + 1) <= $this->getNombre();
            case Operation::TYPE_EGAL :
                return ($this->count + 1) == $this->getNombre();
        }

        return false;
    }

    function __toString()
    {
        return $this->operation->getCode() . ' ' . $this->getNombre() . ' ' . $this->getCritere();
    }

    public function reInit()
    {
        $this->count = 0;
    }
}
