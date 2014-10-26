<?php namespace Tm\TeambuilderBundle\Regles;

use Tm\TeambuilderBundle\Entity\Champion;
use Tm\TeambuilderBundle\Entity\Regle;
use Tm\TeambuilderBundle\Entity\Role;
use Tm\TeambuilderBundle\Regles\Exception\ChampionIntrouvableException;

/**
 * Class TeamBuilder
 * @package Tm\TeambuilderBundle\Regles
 */
class TeamBuilder
{
    const EQUIPE_MOI = 'MOI';

    const EQUIPE_EUX = 'EUX';

    const ACTION_BANNIR = 'BANNIR';

    const ACTION_CHOISIR = 'CHOISIR';

    const ACTION_DEFINIR_ROLE = 'DEFINIR_ROLE';


    /**
     * @var array
     */
    private $listeRegles;

    /**
     * @var array
     */
    private $listeChampions;

    /**
     * @var array
     */
    private $listeChampionsAdverse;

    /**
     * @var array
     */
    private $listeChampionMonEquipe;

    /**
     * @param array $listeRegles
     * @param array $listeChampions
     */
    public function __construct(array $listeRegles, array $listeChampions)
    {
        $this->listeRegles            = $listeRegles;
        $this->listeChampions         = $listeChampions;
        $this->listeChampionsAdverse  = [];
        $this->listeChampionMonEquipe = [];

    }

    /**
     * Retourne les champions restant disponible
     * @return array
     */
    public function getChampionsRestant()
    {
        return $this->listeChampions;
    }

    /**
     * Retourne la liste des champions de mon équipe
     * @return array
     */
    public function getMesChampions()
    {
        return $this->listeChampionMonEquipe;
    }

    /**
     * Indique si mon équipe répond à toute mes règles
     * @return bool
     */
    public function isEquipeOptimale()
    {
        $isOptimale = true;

        $this->flush();

        /** @var Champion $champion */
        foreach ($this->getMesChampions() as $champion) {
            /** @var Regle $regle */
            foreach ($this->listeRegles as $regle) {
                $regle->test($champion, true);
            }
        }

        foreach ($this->listeRegles as $regle) {
            if(!$regle->testNombreOccurences()) {
                return false;
            }

        }
        return $isOptimale;
    }

    /**
     * Renvoi tous les champions qui passe encore les règles optimales
     * @return array
     */
    public function getSuggestions($role = null)
    {

        $this->isEquipeOptimale();

        /** @var Champion $champion */
        foreach ($this->getChampionsRestant() as $champion) {

            $hasRoleRecherche = false;
            foreach($champion->getRoles() as $roleChampion) {
                /** @var Role $roleChampion */
                if($roleChampion->getCode() == $role) {
                    $hasRoleRecherche = true;
                }
            }

            if(!$hasRoleRecherche) {
                $this->supprimerChampion($champion);
                continue;
            }

            /** @var Regle $regle */
            foreach ($this->listeRegles as $regle) {
                $nbRoles = count($champion->getRoles());
                if ($regle->testSuggestion($champion) && !$regle->testNombreOccurences()) {
                    $this->supprimerChampion($champion);
                    break;
                }
            }
        }

        return $this->listeChampions;
    }

    /**
     * Réinitialise le compteur pour chacunes des diférentes règles
     */
    private function flush()
    {
        /** @var Regle $regle */
        foreach ($this->listeRegles as $regle) {
            $regle->reInit();
        }
    }

    /**
     * @param Champion $championABannir
     *
     * @return $this
     */
    public function bannir(Champion $championABannir)
    {
        return $this->supprimerChampion($championABannir);
    }

    /**
     * @param Champion $champion
     *
     * @return $this
     */
    public function ajouterChampionEquipeAdverse(Champion $champion)
    {
        $this->listeChampionsAdverse[] = $champion;

        return $this->supprimerChampion($champion);
    }

    /**
     * @param Champion $champion
     *
     * @return $this
     */
    public function ajouterChampionMonEquipe(Champion $champion)
    {
        $this->listeChampionMonEquipe[] = $champion;

        return $this->supprimerChampion($champion);
    }

    /**
     * @param Champion $championABannir
     *
     * @throws ChampionIntrouvableException
     * @return $this
     */
    private function supprimerChampion(Champion $championABannir)
    {
        $unset = false;

        foreach ($this->listeChampions as $key => $champion) {
            /** @var Champion $champion */

            if ($champion->getId() == $championABannir->getId()) {
                unset($this->listeChampions[$key]);
                $unset = true;

                break;
            }
        }

        # TODO : Remettre ça et gérer au niveau du controller
//        if (!$unset) {
//            throw new ChampionIntrouvableException($champion);
//        }

        return $this;
    }


    public function appliquerAction(array $action, Champion $champion)
    {
        if ($action['action'] == self::ACTION_BANNIR) {
            $this->bannir($champion);
        } else {
            if ($action['action'] == self::ACTION_CHOISIR) {
                if ($action['equipe'] == self::EQUIPE_MOI) {
                    $this->ajouterChampionMonEquipe($champion);
                } else {
                    $this->ajouterChampionEquipeAdverse($champion);
                }
            }
        }
    }
}
