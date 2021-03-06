<?php

namespace Tm\TeambuilderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tm\TeambuilderBundle\Entity\Champion;
use Tm\TeambuilderBundle\Form\ChampionType;
use Tm\TeambuilderBundle\Form\ChampionEditType;
use Tm\TeambuilderBundle\Form\ChampionListeRoleType;
use Tm\TeambuilderBundle\Entity\Role;
use Tm\TeambuilderBundle\Form\RoleType;
use Tm\TeambuilderBundle\Entity\Caracteristique;
use Tm\TeambuilderBundle\Form\CaracteristiqueType;
use Tm\TeambuilderBundle\Entity\TypeAttaque;
use Tm\TeambuilderBundle\Form\TypeAttaqueType;
use Tm\TeambuilderBundle\Entity\Operation;
use Tm\TeambuilderBundle\Form\OperationType;
use Tm\TeambuilderBundle\Entity\Regle;
use Tm\TeambuilderBundle\Form\RegleType;
use Tm\TeambuilderBundle\Regles\Regles;
use Symfony\Component\HttpFoundation\Response;
use Tm\TeambuilderBundle\Regles\TeamBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class TeambuilderController extends Controller
{

    public function indexAction()
    {
        return $this->render('TmTeambuilderBundle:Teambuilder:index.html.twig');
    }

    public function creerequipeAction()
    {
        $request = $this->get('request');
        if($request->isXmlHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            $championRepository = $em->getRepository('TmTeambuilderBundle:Champion');
            $regleRepository = $em->getRepository('TmTeambuilderBundle:Regle');
            $roleRepository = $em->getRepository('TmTeambuilderBundle:Role');
            $listeRegles = $regleRepository->findAll();
            $listeChampions =  $championRepository->findAll();
            $listeRoles = $roleRepository->findAll();
            $teamBuilder = new TeamBuilder($listeRegles, $listeChampions);


            $monEquipe = strtolower($request->request->get('equipe'));
            if($monEquipe == 'blue') {
                $equipeAdverse = 'violet';
            }
            else {
                $equipeAdverse = 'blue';
            }


            $listeChampionsSuggeres = $teamBuilder->getSuggestions();

            $html = [];
            $html['teambuilder'] = $this->renderView('TmTeambuilderBundle:Teambuilder:getteambuilderajax.html.twig', [
                'listeChampionsSuggeres' => $listeChampionsSuggeres,
                'isEquipeOptimale' => $teamBuilder->isEquipeOptimale(),
                'listeChampions' => $listeChampions,
                'monEquipe' => $monEquipe,
                'equipeAdverse' => $equipeAdverse,
                'listeRoles' => $listeRoles,
            ]);


            return (new Response(json_encode($html)));
        }
        else {

            return $this->render('TmTeambuilderBundle:Teambuilder:creerequipe.html.twig');
        }
    }

    public function getsuggestionchampionajaxAction()
    {
        $request = $this->get('request');


        if($request->isXmlHttpRequest()) {
            $actions = $request->getContent();
            $actions = json_decode($actions, true);

            $em = $this->getDoctrine()->getManager();
            $championRepository = $em->getRepository('TmTeambuilderBundle:Champion');
            $regleRepository = $em->getRepository('TmTeambuilderBundle:Regle');

            if( $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
                $listeRegles = $regleRepository->getReglesUtilisateurActuel($this->container->get('security.context')->getToken()->getUser()->getId());
            }
            else {
                $listeRegles = $regleRepository->getReglesPubliques();
            }

            $listeChampions =  $championRepository->findAll();

            $teamBuilder = new TeamBuilder($listeRegles, $listeChampions);

            $roleDefini = false;

            foreach($actions as $key => $action) {

                if($action['action'] == TeamBuilder::ACTION_DEFINIR_ROLE) {
                    $role = $action['role'];
                    $roleDefini = true;
                    continue;
                }

                $champion = $championRepository->find($action['id_champion']);

                $teamBuilder->appliquerAction($action, $champion);
            }

            if($roleDefini == false) {
                $isEquipeOptimale = $teamBuilder->isEquipeOptimale();
            }
            else {
                $listeChampionsSuggeres = $teamBuilder->getSuggestions($role);
            }

            if($roleDefini == false) {
                $data = ['isEquipeOptimale' => []];
                $data['isEquipeOptimale'] = $isEquipeOptimale;
            } else {
                $data = ['suggestions' => []];
                /** @var Champion $championSuggere */
                foreach($listeChampionsSuggeres as $championSuggere) {
                    $data['suggestions'][] = [ 'id' => $championSuggere->getId() ];
                }
            }

            $response = new Response(json_encode($data));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        return new Response();
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function listerchampionAction()
    {
       $em = $this->getDoctrine()
                          ->getManager()
                          ->getRepository('TmTeambuilderBundle:Champion');

       $listeChampions =  $em->getChampionsAvecRelations();

        return $this->render('TmTeambuilderBundle:Teambuilder:listerchampion.html.twig', array('listeChampions' => $listeChampions ));
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function ajouterchampionAction()
    {
        $champion = new Champion();
        $form = $this->createForm(new ChampionType(), $champion);

        $request = $this->get('request');

        if($request->getmethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($champion);
                $em->flush();

                return $this->redirect($this->generateUrl('tm_teambuilder_champion_lister'));
            }
        }

        return $this->render('TmTeambuilderBundle:Teambuilder:ajouterchampion.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimerchampionAction(Champion $champion)
    {
        $em = $this->getDoctrine()
                   ->getManager();

        if($champion === null) {
            throw $this->createNotFoundException('Champion [id='.$champion->getId().'] inexistant');
        }

        $em->remove($champion);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'Champion supprimé');

        return $this->redirect($this->generateUrl('tm_teambuilder_champion_lister'));

    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function modifierchampionAction(Champion $champion)
    {

        if($champion === null) {
            throw $this->createNotFoundException('Champion [id='. $champion->getId().'] inexistant');
        }

        $form = $this->createForm(new ChampionEditType(), $champion);

        $request = $this->get('request');

        if($request->getMethod() == 'POST') {
            $form->submit($request);
            if($this->getTypeAttaque() && getTypeAttque )
            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($champion);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Champion bien modifié');

                return $this->redirect($this->generateUrl('tm_teambuilder_champion_lister'));
            }


        }

        return $this->render('TmTeambuilderBundle:Teambuilder:modifierchampion.html.twig', array(
            'form'     => $form->createView(),
            'champion' => $champion
        ));

    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function ajouterroleAction()
    {
        $role = new Role();
        $form = $this->createForm(new RoleType(), $role);

        $request = $this->get('request');

        if($request->getmethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($role);
                $em->flush();

                return $this->redirect($this->generateUrl('tm_teambuilder_role_lister'));
            }

        }

        return $this->render('TmTeambuilderBundle:Teambuilder:ajouterrole.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function listerroleAction()
    {
        $em = $this->getDoctrine()
            ->getManager()
            ->getRepository('TmTeambuilderBundle:Role');

        $listeRoles =  $em->findAll();


        return $this->render('TmTeambuilderBundle:Teambuilder:listerrole.html.twig', array('listeRoles' => $listeRoles ));
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimerroleAction(Role $role)
    {
        $em = $this->getDoctrine()
            ->getManager();

        if($role === null) {
            throw $this->createNotFoundException('Rôle [id='.$role->getId().'] inexistant');
        }

        $em->remove($role);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'Role supprimé');

        return $this->redirect($this->generateUrl('tm_teambuilder_role_lister'));

    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function modifierroleAction(Role $role)
    {

        if($role === null) {
            throw $this->createNotFoundException('Rôle [id='. $role->getId().'] inexistant');
        }

        $form = $this->createForm(new RoleType(), $role);

        $request = $this->get('request');

        if($request->getMethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($role);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Rôle bien modifié');
                return $this->redirect($this->generateUrl('tm_teambuilder_role_lister'));
            }
        }

        return $this->render('TmTeambuilderBundle:Teambuilder:modifierrole.html.twig', array(
            'form'     => $form->createView(),
            'role' => $role
        ));

    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function ajoutercaracteristiqueAction()
    {
        $caracteristique = new Caracteristique();
        $form = $this->createForm(new CaracteristiqueType(), $caracteristique);

        $request = $this->get('request');

        if($request->getmethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($caracteristique);
                $em->flush();

                return $this->redirect($this->generateUrl('tm_teambuilder_caracteristique_lister'));
            }

        }

        return $this->render('TmTeambuilderBundle:Teambuilder:ajoutercaracteristique.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function listercaracteristiqueAction()
    {
        $em = $this->getDoctrine()
            ->getManager()
            ->getRepository('TmTeambuilderBundle:Caracteristique');

        $listeCaracteristiques =  $em->findAll();


        return $this->render('TmTeambuilderBundle:Teambuilder:listercaracteristique.html.twig', array('listeCaracteristiques' => $listeCaracteristiques ));
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimercaracteristiqueAction(Caracteristique $caracteristique)
    {
        $em = $this->getDoctrine()
            ->getManager();

        if($caracteristique === null) {
            throw $this->createNotFoundException('Caractéristique [id='.$caracteristique->getId().'] inexistante');
        }

        $em->remove($caracteristique);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'Caractéristique supprimée');

        return $this->redirect($this->generateUrl('tm_teambuilder_caracteristique_lister'));

    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function modifiercaracteristiqueAction(Caracteristique $caracteristique)
    {

        if($caracteristique === null) {
            throw $this->createNotFoundException('Caractéristique [id='. $caracteristique->getId().'] inexistante');
        }

        $form = $this->createForm(new CaracteristiqueType(), $caracteristique);

        $request = $this->get('request');

        if($request->getMethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($caracteristique);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Caractéristique bien modifiée');

                return $this->redirect($this->generateUrl('tm_teambuilder_caracteristique_lister'));
            }
        }

        return $this->render('TmTeambuilderBundle:Teambuilder:modifiercaracteristique.html.twig', array(
            'form'     => $form->createView(),
            'role' => $caracteristique
        ));

    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function ajoutertypeattaqueAction()
    {
        $typeAttaque = new TypeAttaque();
        $form = $this->createForm(new TypeAttaqueType(), $typeAttaque);

        $request = $this->get('request');

        if($request->getmethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($typeAttaque);
                $em->flush();

                return $this->redirect($this->generateUrl('tm_teambuilder_typeattaque_lister'));
            }
        }

        return $this->render('TmTeambuilderBundle:Teambuilder:ajoutertypeattaque.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function listertypeattaqueAction()
    {
        $em = $this->getDoctrine()
            ->getManager()
            ->getRepository('TmTeambuilderBundle:TypeAttaque');

        $listeTypeAttaques =  $em->findAll();


        return $this->render('TmTeambuilderBundle:Teambuilder:listertypeattaque.html.twig', array('listeTypeAttaques' => $listeTypeAttaques ));
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimertypeattaqueAction(TypeAttaque $typeAttaque)
    {
        $em = $this->getDoctrine()
            ->getManager();

        if($typeAttaque === null) {
            throw $this->createNotFoundException('Type d\'attaque [id='.$typeAttaque->getId().'] inexistant');
        }

        $em->remove($typeAttaque);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'Type d\'attaque supprimée');

        return $this->redirect($this->generateUrl('tm_teambuilder_typeattaque_lister'));

    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function modifiertypeattaqueAction(TypeAttaque $typeAttaque)
    {

        if($typeAttaque === null) {
            throw $this->createNotFoundException('Type d\'attaque [id='. $typeAttaque->getId().'] inexistant');
        }

        $form = $this->createForm(new TypeAttaqueType(), $typeAttaque);

        $request = $this->get('request');

        if($request->getMethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($typeAttaque);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Type d\'attaque bien modifiée');

                return $this->redirect($this->generateUrl('tm_teambuilder_typeattaque_lister'));
            }
        }

        return $this->render('TmTeambuilderBundle:Teambuilder:modifiertypeattaque.html.twig', array(
            'form'     => $form->createView(),
            'role' => $typeAttaque
        ));

    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function ajouteroperationAction()
    {
        $operation = new Operation();
        $form = $this->createForm(new OperationType(), $operation);

        $request = $this->get('request');

        if($request->getmethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($operation);
                $em->flush();

                return $this->redirect($this->generateUrl('tm_teambuilder_operation_lister'));
            }

        }

        return $this->render('TmTeambuilderBundle:Teambuilder:ajouteroperation.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function listeroperationAction()
    {
        $em = $this->getDoctrine()
            ->getManager()
            ->getRepository('TmTeambuilderBundle:Operation');

        $listeOperations =  $em->findAll();


        return $this->render('TmTeambuilderBundle:Teambuilder:listeroperation.html.twig', array('listeOperations' => $listeOperations ));
    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function supprimeroperationAction(Operation $operation)
    {
        $em = $this->getDoctrine()
            ->getManager();

        if($operation === null) {
            throw $this->createNotFoundException('Opération [id='.$operation->getId().'] inexistante');
        }

        $em->remove($operation);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'Opération supprimée');

        return $this->redirect($this->generateUrl('tm_teambuilder_operation_lister'));

    }

    /**
     *  @Security("has_role('ROLE_ADMIN')")
     */
    public function modifieroperationAction(Operation $operation)
    {

        if($operation === null) {
            throw $this->createNotFoundException('Operation [id='. $operation->getId().'] inexistante');
        }

        $form = $this->createForm(new OperationType(), $operation);

        $request = $this->get('request');

        if($request->getMethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($operation);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Opération bien modifiée');

                return $this->redirect($this->generateUrl('tm_teambuilder_operation_lister'));
            }
        }

        return $this->render('TmTeambuilderBundle:Teambuilder:modifieroperation.html.twig', array(
            'form'     => $form->createView(),
            'role' => $operation
        ));

    }

    /**
     *  @Security("has_role('ROLE_UTILISATEUR')")
     */
    public function ajouterregleAction()
    {
        $regle = new Regle();

        $form = $this->createForm(new RegleType(), $regle);

        $request = $this->get('request');

        if($request->getmethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $equipeRepository = $em->getRepository('TmTeambuilderBundle:Equipe');

                $regle->setEquipe($equipeRepository->getEquipeUtilisateurActuel($this->container->get('security.context')->getToken()->getUser()->getId()));
                $em->persist($regle);
                $em->flush();

                return $this->redirect($this->generateUrl('tm_teambuilder_regle_lister'));
            }

        }

        return $this->render('TmTeambuilderBundle:Teambuilder:ajouterregle.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *  @Security("has_role('ROLE_UTILISATEUR')")
     */
    public function listerregleAction()
    {
        $em = $this->getDoctrine()
            ->getManager();
        $regleRepository = $em->getRepository('TmTeambuilderBundle:Regle');

        $listeRegles =  $regleRepository->getReglesUtilisateurActuel($this->container->get('security.context')->getToken()->getUser()->getId());

        return $this->render('TmTeambuilderBundle:Teambuilder:listerregle.html.twig', array('listeRegles' => $listeRegles ));
    }

    /**
     *  @Security("has_role('ROLE_UTILISATEUR')")
     */
    public function supprimerregleAction(Regle $regle)
    {
        $em = $this->getDoctrine()
            ->getManager();

        if($regle === null) {
            throw $this->createNotFoundException('Régle [id='.$regle->getId().'] inexistante');
        }

        $em->remove($regle);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'Régle supprimée');

        return $this->redirect($this->generateUrl('tm_teambuilder_regle_lister'));

    }

    /**
     *  @Security("has_role('ROLE_UTILISATEUR')")
     */
    public function modifierregleAction(Regle $regle)
    {

        if($regle === null) {
            throw $this->createNotFoundException('Regle [id='. $regle->getId().'] inexistante');
        }

        $form = $this->createForm(new RegleType(), $regle);

        $request = $this->get('request');

        if($request->getMethod() == 'POST') {
            $form->submit($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($regle);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'Règle bien modifiée');

                return $this->redirect($this->generateUrl('tm_teambuilder_regle_lister'));
            }
        }

        return $this->render('TmTeambuilderBundle:Teambuilder:modifierregle.html.twig', array(
            'form'     => $form->createView(),
            'role' => $regle
        ));

    }


}
