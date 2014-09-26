<?php namespace Tm\TeambuilderBundle\Regles\Exception;

use Exception;
use Tm\TeambuilderBundle\Entity\Champion;

class ChampionIntrouvableException extends Exception
{
    function __construct(Champion $champion)
    {
        parent::__construct('Impossible de trouver le champion dont l\'id est [' . $champion->getId() . ']');
    }
}