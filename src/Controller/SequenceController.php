<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Sequence;
use App\Repository\SequenceRepository;
use App\Entity\Axe;
use App\Entity\Twin;
use Doctrine\Common\Collections\ArrayCollection;

class SequenceController extends AbstractController
{
        
    /**
     *
     * @Route("/sequence/save", name="sequence_save")                   
     */
    ///////
    public function enregistrer(Request $request , EntityManagerInterface $manager)
    {
        $file = "fichier.txt";
        if (file_exists($file)) 
        {
            $sequence = file_get_contents($file);
       
             
            $nomSequence = "sequence";
            $i = 1;
            while (file_exists($nomSequence)) 
            {
                                                                //tant que le fichier séquence existe
                $nomSequence = "sequence" . $i;                 //on affecte à la variable nomSequence le nom "sequence" suivie de la variable $i.
                $i ++;                                          // la variable "$i" s'incrémente de 1 à chaque fois 
            }
            file_put_contents($nomSequence, $sequence);
            //////Enregistrement du fichier dans la bdd///////////////////////
            $sequenceBdd=new Sequence();
            $url =  $_SERVER['DOCUMENT_ROOT'].$nomSequence;         //url de la séquence enregistrée
            $dateJour=new \DateTime();
            
            $sequenceBdd->setUrl($url);                             
            $sequenceBdd->setDate($dateJour);
            
            $manager->persist($sequenceBdd);
            $manager->flush();
        }
        
        
          /*return $this->render('sequence/afficher.html.twig', [
          'controller_name' => 'SequenceController'
          // 'sequence'=>$sequenceBdd,
          ]);*/
         
        return $this->redirectToRoute('sequence_afficher', [
            'nomFichierSequence' => $nomSequence
        ]);
    }

    /**
     *
     * @Route("/sequence/afficher/{nomFichierSequence}", name="sequence_afficher")
     */
    public function afficher($nomFichierSequence)
    {
        $file = $nomFichierSequence;
        if (file_exists($file)) 
        {
            $sequence = file_get_contents($file);
        }
        return $this->render('sequence/afficher.html.twig', [
            'controller_name' => 'SequenceController',
            'sequence' => $sequence
        ]);
    }

    /**
     *
     * @Route("/sequence/selectionner", name="sequence_selectionner")
     */
    public function selectionner()                          //sélection des séquences pour les afficher et les supprimer    
    {
        $listesFichiers = array();                          //création d'un tableau vide
        $tab = scandir("../public");                        //Scan du répertoire où est enregistrée la séquence
        for ($i = 0; $i < count($tab); $i ++) 
        {
            if (substr($tab[$i], 0, 8) == "sequence")       //si les 8 premiers caractères du fichier portent le nom "sequence"
            {
                array_push($listesFichiers, $tab[$i]);      //on insère tous les fichiers commençant par "sequence" dans le tableau
            }
        }

        return $this->render('sequence/formulaire.html.twig', [     //Renvoie vers la page "formulaire.html.twig"
            'controller_name' => 'SequenceController',
            'listeFichiers' => $listesFichiers
        ]);
    }
    
    /**
     *
     * @Route("/sequence/selectionner1", name="sequence_selectionner1")
     */
    public function selectionner1()                         //sélection des séquences pour les jouer
    {
        $listesFichiers = array();                          //création d'un tableau vide
        $tab = scandir("../public");                        //Scan du répertoire où est enregistrée la séquence
        for ($i = 0; $i < count($tab); $i ++)
        {
            if (substr($tab[$i], 0, 8) == "sequence")       //si les 8 premiers caractères du fichier portent le nom "sequence"
            {
                array_push($listesFichiers, $tab[$i]);      //on insère tous les fichiers commençant par "sequence" dans le tableau
            }
        }
        
        return $this->render('sequence/formulaire1.html.twig', [        //on est redirigés sur la page "formulaire1.html.twig" pour jouer la séquence
            'controller_name' => 'SequenceController',
            'listeFichiers' => $listesFichiers,
        ]);
    }
    
    
    /**
     *
     * @Route("/sequence/jouer", name="sequence_jouer")
     */
    public function jouer()                     
    {
        $sequence=$_POST["selection1"];
        $axe1=new Axe();
        $axe2=new Axe();
        $axe3=new Axe();
        $axe4=new Axe();
        $axe5=new Axe();
        $axe6=new Axe();
        
       ////en cours, pas terminé/////////////////////////////
        
        
        return $this->render('sequence/formulaire1.html.twig', [
            'controller_name' => 'SequenceController',
            'tableau' => $tabTwin,
        ]);
    }

    /**
     *
     * @Route("/sequence/Traitement", name="sequence_traitement")
     */
    public function traitementFormulaire()                          //Traitement du formulaire pour supprimer et afficher les séquences
    {
            $sequence = $_POST["selection"];                            //on récupère la séquence que l'on a sélectionné dans le formulaire
            if (isset($_POST["afficher"]))                              //si on a appuyé sur "afficher"
            {
                return $this->redirectToRoute('sequence_afficher', [    // on est redirigé vers la route "sequence_afficher" qui va afficher la séquence que l'on a sélectionnée
                    "nomFichierSequence" => $sequence
                ]);
            }
            else if (isset($_POST["supprimer"]))                        //sinon si on a appuyé sur "supprimer"
            {
                return $this->redirectToRoute('sequence_suppression', [ //on va être redirigé vers la route "sequence_suppression"
                    "nomFichierSequence" => $sequence
                ]);
            }
    }
        


    /**
     *
     * @Route("/sequence/suppression/{nomFichierSequence}", name="sequence_suppression")
     */
    public function suppressionFichier($nomFichierSequence, EntityManagerInterface $manager, SequenceRepository $repo)
    {   
        $url =  $_SERVER['DOCUMENT_ROOT'].$nomFichierSequence;      //url de la séquence enregistrée en base de données
        unlink($nomFichierSequence);                                //suppression du fichier
        $sequence=new Sequence();
        $sequence=$repo->findOneBy(array("url"=>$url));             //recherche dans la base de données l'enregistrement qui a l'url "$url"
        $manager->remove($sequence);
        $manager->flush();
        return $this->redirectToRoute('sequence_selectionner');
    }
}
