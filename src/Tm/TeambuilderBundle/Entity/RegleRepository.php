<?php

namespace Tm\TeambuilderBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CommentaireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RegleRepository extends EntityRepository
{
    public function getReglesUtilisateurActuel($idUtilisateur)
    {
        $qb = $this->createQueryBuilder('r')
            ->where('u.id = :id ')
            ->setParameter('id', $idUtilisateur)
            ->join('r.equipe', 'e')
            ->join('e.utilisateur', 'u')
            ->addSelect('r');

        return $qb->getQuery()->getResult();
    }

    public function getReglesPubliques()
    {
        $qb = $this->createQueryBuilder('r')
            ->where('e.IS_PUBLIC = 1')
            ->join('r.equipe', 'e')
            ->addSelect('r');

        return $qb->getQuery()->getResult();
    }
}
