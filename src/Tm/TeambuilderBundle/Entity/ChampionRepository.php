<?php

namespace Tm\TeambuilderBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ChampionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChampionRepository extends EntityRepository
{
    public function getChampionsAvecRelations()
    {
        $qb = $this->createQueryBuilder('c');
        $qb //->join('c.caracteristiques', 'ca')
            ->join('c.roles', 'r')
            ->join('c.typeAttaques', 't')
            ->join('c.roles', 'co')
            //->addSelect('ca')
            ->addSelect('r')
            ->addSelect('t')
            ->addSelect('co');

        return $qb->getQuery()->getResult();

    }

   public function rechercheChampionParMotCle($motCle)
   {
      $qb = $this->createQueryBuilder('c');
      $qb->where("c.nom LIKE :motCle")
         ->setParameter('motCle', '%'.$motCle.'%');

      return $qb->getQuery()->getResult();
   }
}
