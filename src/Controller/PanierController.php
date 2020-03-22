<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



class PanierController extends AbstractController
{
    /**
     * @Route("/", name="panier")
     */
    public function index(Request $request)
    {   
        $panier = new Panier();
        $form = $this->createForm(PanierType::class, $panier);
        
        $pdo = $this->getDoctrine()->getManager();

        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid() ){
            $pdo->persist($panier);
            $pdo->flush();

            $this->addFlash("success", "Panier sauvegardé");
        }

        $paniers = $pdo->getRepository(Panier::class)->findAll();


        return $this->render('panier/index.html.twig', [
            'paniers' => $paniers,
            'form_panier' => $form->createView()
        ]);
    }

    /**
     * @Route("/panier/{id}", name="mon_panier")
     */

    public function panier(Request $request, Panier $panier=null){
        
        if($panier != null){
            $form = $this->createForm(PanierType::class, $panier);
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){
                $pdo = $this->getDoctrine()->getManager();
                $pdo->persist($panier);
                $pdo->flush();

                $this->addFlash("success", "Panier mise à jour");
            }
    
            return $this->render('panier/panier.html.twig', [
                'form' => $form->createView()
            ]);
        }
        else{
            $this->addFlash("danger", "panier introuvable");
            return $this->redirectToRoute('panier');
        }

    }

    /**
     * @Route("/panier/delete/{id}", name="delete_panier")
     */
    public function delete(Panier $panier=null){

        if($panier != null){
            $pdo = $this->getDoctrine()->getManager();
            $pdo->remove($panier); // Suppression
            $pdo->flush();

            $this->addFlash("success", "Panier supprimée");
        }
        else{
            $this->addFlash("danger", "Panier introuvable");
        }
        // Dans tous les cas, on redirige vers les paniers
        return $this->redirectToRoute('panier');
    }
}
