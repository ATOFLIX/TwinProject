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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

const DOSSIER_SEQUENCES = "sequences";
class SequenceController extends AbstractController
{
        
    

    /**
     *
     * @Route("/sequence/afficher/{nomFichierSequence}", name="sequence_afficher")
     */
    public function afficher($nomFichierSequence)
    {
        $file = DOSSIER_SEQUENCES.DIRECTORY_SEPARATOR.$nomFichierSequence;
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
        $tab = scandir(DOSSIER_SEQUENCES);              //Scan du répertoire où est enregistrée la séquence
        for ($i = 0; $i < count($tab); $i ++) 
        {
            if(substr($tab[$i],0,1)!=".")
            {
                array_push($listesFichiers, $tab[$i]); // on insère tous les fichiers commençant par "sequence" dans le tableau
            }
        }

        return $this->render('sequence/formulaire.html.twig', [     //Renvoie vers la page "formulaire.html.twig"
            'controller_name' => 'SequenceController',
            'listeFichiers' => $listesFichiers
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
     * @Route("/sequence/TraitementNom", name="sequence_traitementNom")
     */
    public function traitementNomSequence(Request $request, EntityManagerInterface $manager)
    {
        $nomSequence="";
        $filesystem = new Filesystem();
        $pathcourant = getcwd();
        $filesystem->mkdir(DOSSIER_SEQUENCES, 0777);
        
        $file = $pathcourant . DIRECTORY_SEPARATOR . "fichier.txt";
        
        
        
        $form=$this->createFormBuilder()->add('nomSequence',TextType::class, ['required'=> false ])
        ->add('OK',SubmitType::class)->getForm();
        $form->handleRequest($request);
        
        
        
        if($form->isSubmitted() && $form->isValid())
        {
            $donnees=$form->getData();
            $nomSequence=$donnees['nomSequence'];
            if(!$nomSequence)
            {
                $nomSequence="sequence";
            }
            $tailleSequence=strlen($nomSequence);
            
            $nom=substr($nomSequence,0,$tailleSequence);
            
            if ($filesystem->exists($file))
            {
                $i=1;
                $sequence = file_get_contents($file);
                
                while (file_exists(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence))
                {
                    
                    $nomSequence = $nom. $i;
                    $i ++;
                    
                    
                    
                }
                
                
                
                $filesystem->dumpFile(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence, $sequence);
                // ////Enregistrement du fichier dans la bdd///////////////////////
                $sequenceBdd = new Sequence();
                $url = $pathcourant . DIRECTORY_SEPARATOR . DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence;
                // $url = $_SERVER['DOCUMENT_ROOT'].$nomSequence; //url de la séquence enregistrée
                $dateJour = new \DateTime();
                
                $sequenceBdd->setUrl($url);
                $sequenceBdd->setDate($dateJour);
                
                $manager->persist($sequenceBdd);
                $manager->flush();
                
                
                return $this->render('sequence/enregistrer.html.twig', [
                    'controller_name' => 'SequenceController',
                    'nomSequence'=>$nomSequence,
                    'form'=>$form->createView(),
                ]);
            }
            
        }
        
        
        return $this->render('sequence/enregistrer.html.twig', [
            'controller_name' => 'SequenceController',
            'form' => $form->createView(),
            'nomSequence'=>$nomSequence,
        ]);
    }   
        
        
        
        
        

    /**
     *
     * @Route("/sequence/suppression/{nomFichierSequence}", name="sequence_suppression")
     */
    public function suppressionFichier($nomFichierSequence, EntityManagerInterface $manager, SequenceRepository $repo)
    {
        $pathcourant=getcwd();
        $url = $pathcourant.DIRECTORY_SEPARATOR .DOSSIER_SEQUENCES.DIRECTORY_SEPARATOR.$nomFichierSequence;
        //$url =  $_SERVER['DOCUMENT_ROOT'].$nomFichierSequence;      //url de la séquence enregistrée en base de données
        unlink(DOSSIER_SEQUENCES.DIRECTORY_SEPARATOR.$nomFichierSequence);                                //suppression du fichier
        $sequence=new Sequence();
        $sequence=$repo->findOneBy(array("url"=>$url));             //recherche dans la base de données l'enregistrement qui a l'url "$url"
        $manager->remove($sequence);
        $manager->flush();
        return $this->redirectToRoute('sequence_selectionner');
    }
    
}
