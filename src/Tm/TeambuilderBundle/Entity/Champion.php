<?php

namespace Tm\TeambuilderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Champion
 *
 * @ORM\Table(name="champion")
 * @ORM\Entity(repositoryClass="Tm\TeambuilderBundle\Entity\ChampionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Champion
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
     * @ORM\Column(name="NOM", type="string", length=64, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="NOM_FICHIER_IMAGE", type="string", length=255, nullable=false)
     */
    private $nomFichierImage;

    /**
     * @ORM\ManyToMany(targetEntity="Tm\TeambuilderBundle\Entity\Role")
     * @ORM\JoinTable(name="champion_role",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ID_CHAMPION", referencedColumnName="ID")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ID_ROLE", referencedColumnName="ID")
     *   }
     * )
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Caracteristique")
     * @ORM\JoinTable(name="champion_caracteristique",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ID_CHAMPION", referencedColumnName="ID")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ID_CARACTERISTIQUE", referencedColumnName="ID")
     *   }
     * )
     */
    private $caracteristiques;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Champion")
     * @ORM\JoinTable(name="champion_contre",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ID_CHAMPION", referencedColumnName="ID")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ID_CONTRE", referencedColumnName="ID")
     *   }
     * )
     */
    private $contres;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="TypeAttaque",)
     * @ORM\JoinTable(name="champion_type_attaque",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ID_CHAMPION", referencedColumnName="ID")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="ID_TYPE_ATTAQUE", referencedColumnName="ID")
     *   }
     * )
     */
    private $typeAttaques;

    /**
     * @var date
     *
     * @ORM\Column(name="DATE", type="date", nullable=false)
     */
    private $date;

    private $file;

    private $oldFile;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->caracteristiques = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contres = new \Doctrine\Common\Collections\ArrayCollection();
        $this->typeAttaques = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set nom
     *
     * @param string $nom
     * @return Champion
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set nomFichierImage
     *
     * @param string $nomFichierImage
     * @return Champion
     */
    public function setNomFichierImage($nomFichierImage)
    {
        $this->nomFichierImage = $nomFichierImage;

        return $this;
    }

    /**
     * Get nomFichierImage
     *
     * @return string 
     */
    public function getNomFichierImage()
    {
        return $this->nomFichierImage;
    }

    /**
     * Get roles
     *
     * @return \Tm\TeambuilderBundle\Entity\Role
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add role
     *
     * @param \Tm\TeambuilderBundle\Entity\Role $role
     * @return Champion
     */
    public function addRole(\Tm\TeambuilderBundle\Entity\Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param \Tm\TeambuilderBundle\Entity\Role $role
     */
    public function removeRole(\Tm\TeambuilderBundle\Entity\Role $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Add caracteristique
     *
     * @param \Tm\TeambuilderBundle\Entity\Caracteristique $caracteristique
     * @return Champion
     */
    public function addCaracteristique(\Tm\TeambuilderBundle\Entity\Caracteristique $caracteristique)
    {
        $this->caracteristiques[] = $caracteristique;

        return $this;
    }

    /**
     * Remove caracteristique
     *
     * @param \Tm\TeambuilderBundle\Entity\Caracteristique $caracteristique
     */
    public function removeCaracteristique(\Tm\TeambuilderBundle\Entity\Caracteristique $caracteristique)
    {
        $this->caracteristiques->removeElement($caracteristique);
    }

    /**
     * Get caracteristiques
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCaracteristiques()
    {
        return $this->caracteristiques;
    }

    /**
     * Add contre
     *
     * @param \Tm\TeambuilderBundle\Entity\Champion $contre
     * @return Champion
     */
    public function addContre(\Tm\TeambuilderBundle\Entity\Champion $contre)
    {
        $this->contres[] = $contre;

        return $this;
    }


    /**
     * Get contre
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContres()
    {
        return $this->contres;
    }

    /**
     * Remove contre
     *
     * @param \Tm\TeambuilderBundle\Entity\Champion $contre
     */
    public function removeContre(\Tm\TeambuilderBundle\Entity\Champion $contre)
    {
        $this->contres->removeElement($contre);
    }

    /**
     * Add typeAttaque
     *
     * @param \Tm\TeambuilderBundle\Entity\TypeAttaque $typeAttaque
     * @return Champion
     */
    public function addTypeAttaque(\Tm\TeambuilderBundle\Entity\TypeAttaque $typeAttaque)
    {
        $this->typeAttaques[] = $typeAttaque;

        return $this;
    }

    /**
     * Remove typeAttaque
     *
     * @param \Tm\TeambuilderBundle\Entity\TypeAttaque $typeAttaque
     */
    public function removeTypeAttaque(\Tm\TeambuilderBundle\Entity\TypeAttaque $typeAttaque)
    {
        $this->typeAttaques->removeElement($typeAttaque);
    }

    /**
     * Get typeAttaque
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTypeAttaques()
    {
        return $this->typeAttaques;
    }


    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Champion
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    public function setFile(UploadedFile $file)
    {
        $this->date = new \DateTime("now");
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath($nomFichierImage)
    {
        return null === $nomFichierImage ? null : $this->getUploadRootDir().'/'.$nomFichierImage;
    }

    public function getWebPath()
    {
        return null === $this->nomFichierImage ? null : $this->getUploadDir().'/'.$this->nomFichierImage;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/img/champions';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {

            if(null !== $this->nomFichierImage)
                $this->oldFile = $this->nomFichierImage;

            $this->nomFichierImage = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }
        // s'il y a une erreur lors du déplacement du fichier, une exception
        // va automatiquement être lancée par la méthode move(). Cela va empêcher
        // proprement l'entité d'être persistée dans la base de données si
        // erreur il y a
        $this->file->move($this->getUploadRootDir(), $this->nomFichierImage);

        unset($this->file);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath($this->nomFichierImage)) {
            unlink($file);
        }
    }

    /**
     * @ORM\Postupdate()
     */
    public function removeUploadBeforeUpdate()
    {
        if ($file = $this->getAbsolutePath($this->oldFile)) {
            unlink($file);
        }
    }

    public function hasRole(Role $role)
    {
        foreach ($this->roles as $roleChampion) {
            if ($roleChampion->getCode() == $role->getCode()) {
                return true;
            }
        }

        return false;
    }

    public function hasCaracteristique(Caracteristique $caracteristique)
    {
        foreach ($this->caracteristiques as $caracteristiqueChampion) {
            if ($caracteristiqueChampion->getCode() == $caracteristique->getCode()) {
                return true;
            }
        }

        return false;
    }

    public function hasTypeAttaque(TypeAttaque $typeAttaque)
    {
        foreach ($this->typeAttaques as $typeAttaqueChampion) {
            if ($typeAttaqueChampion->getCode() == $typeAttaque->getCode()) {
                return true;
            }
        }

        return false;
    }

    function __toString()
    {
        $typesAttaque = implode(', ', $this->getTypeAttaques()->toArray());
        $roles = implode(', ', $this->getRoles()->toArray());
        $caracteristiques = implode(', ', $this->getCaracteristiques()->toArray());

        return $this->getNom() . ' [TypeAttaque=' . $typesAttaque . ', Role=' . $roles . ', Caracteristique=' . $caracteristiques . ']';
    }
}
