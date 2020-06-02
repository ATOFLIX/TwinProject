<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Sequence;
use App\Repository\SequenceRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\Form\FormTypeInterface;

const DOSSIER_SEQUENCES = "sequences";

class SequenceController extends AbstractController
{
    
    
    private function Recup($nom)
    {
        
        if (empty($nom)) {
            $nom = "sequence";
        }
        
        $nom = str_replace(' ', '', $nom);
        $tailleSequence = strlen($nom);
        $nomSequence = substr($nom, 0, $tailleSequence);
        
        $i = 1;
        
        while (file_exists(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence . ".json")) {
            
            $nomSequence = $nom . $i;
            ++$i;
        }
        $nomSequence = $nomSequence . ".json";
        
        return $nomSequence;
    }
    
    
    private function fluxJson($nom)
    {
        $nom1=explode(",", $nom);
        //$tab1=array_chunk($nom, 6);
        return json_encode($nom1);
        
    }
    
    
    
    
    /**
     *
     * @Route("/sequence/save/", name="sequence_save")
     */
    public function SequenceSave()
    {
        return $this->render('sequence/save.html.twig', [
            'controller_name' => 'SequenceController'
        ]);
    }
    
    /**
     *
     * @Route("/sequence/rename/{nomFichierSequence}", name="sequence_rename")
     */
    public function rename($nomFichierSequence, Request $request, SequenceRepository $repo, EntityManagerInterface $manager)
    {
        $pathcourant = getcwd();
        // création du formulaire avec les différents champs leurs types, leurs contraintes et attributs , et le submit
        $form = $this->createFormBuilder()
        ->add('nom', TextType::class, [
            'required' => false,
            'data' => substr($nomFichierSequence, 0, - 5)
        ])
        ->add('Valider', SubmitType::class)
        ->getForm();
        // on demande au formulaire d'analyser les infos de la requête et de récupérer les saisies en cas de submit
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData(); // retourne les données du formulaire dans un tableau
            $nom=$donnees['nom'];
            $nomSequence=$this->Recup($nom);
            //$nom = $donnees['nom'];
            /*if (empty($nom)) {
                $nom = "sequence";
            }
            $nom = str_replace(' ', '', $nom);
            $tailleSequence = strlen($nom);
            $nom = substr($nom, 0, $tailleSequence);
            
            $i = 1;*/
            
            //dump($nom);
            while (file_exists(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence . ".json")) {
                
                $nomSequence = $nom . $i;
                $i ++;
            }
            $nomSequence = $nomSequence . ".json";
            rename(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomFichierSequence, DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence);
            // ////Enregistrement du fichier dans la bdd///////////////////////
            $sequenceBdd = $repo->findOneURLByEnd($nomFichierSequence);
            // dump($sequenceBdd);
            $url = $pathcourant . DIRECTORY_SEPARATOR . DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence;
            // dump($nomSequence);
            $sequenceBdd->setUrl($url);
            
            $manager->persist($sequenceBdd);
            $manager->flush();
            $this->addFlash("success", "La séquence " . $nomFichierSequence . " a été renommée en " . $nomSequence);
            return $this->redirectToRoute('sequence_selectionnerSequence');
        }
        return $this->render('sequence/sequenceRenommer.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    
    /**
     *
     * @Route("/sequence/traitement1/{nomFichierSequence}", name="sequence_traitement1")
     */
    public function script1($nomFichierSequence=null, Request $request, SequenceRepository $repo, EntityManagerInterface $manager)
    {
        //$nomSequence = $nomFichierSequence;
        $filesystem = new Filesystem();
        $pathcourant = getcwd();
        $filesystem->mkdir(DOSSIER_SEQUENCES, 0777);
        // création du formulaire avec les différents champs leurs types, leurs contraintes et attributs , et le submit
        $form = $this->createFormBuilder()
        ->add('nom', TextType::class, [
            'required' => false,
            //'data', HiddenType::class
        ]
            
           )
        ->add('data', HiddenType::class)
        ->getForm();
        // on demande au formulaire d'analyser les infos de la requête et de récupérer les saisies en cas de submit
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData(); // retourne les données du formulaire dans un tableau
            
            $nomSequence = $donnees['nom'];
            $flux = $donnees['data'];
            $nomSequence=$this->Recup($nomSequence);
            $fluxJson=$this->fluxJson($flux);
            //$nom=$this->Recup1($nom);
            
            /*if (empty($nomSequence)) {
                $nomSequence = "sequence";
            }
            
            $nomSequence = str_replace(' ', '', $nomSequence);
            $tailleSequence = strlen($nomSequence);
            $nom = substr($nomSequence, 0, $tailleSequence);
            
            $i = 1;
            
            $flux = explode(",", $flux);
            $tab1=array_chunk($flux, 6);
            $fluxJson = json_encode($tab1);
            //dump($nomSequence);
            while (file_exists(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence . ".json")) {
                
                $nomSequence = $nom . $i;
                $i ++;
            }
            $nomSequence = $nomSequence . ".json";*/
            $filesystem->dumpFile(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence, $fluxJson);
            // ////Enregistrement du fichier dans la bdd///////////////////////
            $sequenceBdd = new Sequence();
            //$sequenceBdd = $repo->findOneURLByEnd($nomFichierSequence);
            // dump($sequenceBdd);
            $url = $pathcourant . DIRECTORY_SEPARATOR . DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence;
            // dump($nomSequence);
            date_default_timezone_set('Europe/Paris');
            $dateJour = new \DateTime();
            $sequenceBdd->setUrl($url);
            //dump($dateJour);
            
            $sequenceBdd->setDate($dateJour);
            $manager->persist($sequenceBdd);
            $manager->flush();
            $this->addFlash("success", "La séquence " . " \"".$nomSequence. "\"  a bien été enregistrée");
            return $this->redirectToRoute('sequence_selectionnerSequence');
           
        }
        return $this->render('sequence/save.html.twig', [
            //'controller_name' => 'SequenceController',
            'form' => $form->createView(),
            
        ]);
    
    }
    
    
    
    
    
    
    
    
    
    /**
     *
     * @Route("/sequence/traitement/{nomFichierSequence}", name="sequence_traitement")
     */
    /*public function script($nomFichierSequence = null, Request $request, SequenceRepository $repo, EntityManagerInterface $manager)
    {
        // $nomSequence=$nomFichierSequence;
        if (isset($_GET['data'])) {
            $nomSequence = $_GET['nomsequence'];
            
            $flux = $_GET['data'];
            // dump($flux);
            
            if (empty($nomSequence)) {
                $nomSequence = "sequence";
            }
            // $nomSequence = $nomFichierSequence;
            $filesystem = new Filesystem();
            $pathcourant = getcwd();
            
            $filesystem->mkdir(DOSSIER_SEQUENCES, 0777);
            $nomSequence = str_replace(' ', '', $nomSequence);
            $tailleSequence = strlen($nomSequence);
            $nom = substr($nomSequence, 0, $tailleSequence);
            
            $i = 1;
            
            $flux = explode(",", $flux);
            $fluxJson = json_encode($flux);
            // dump($fluxJson);
            while (file_exists(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence . ".json")) {
                
                $nomSequence = $nom . $i;
                $i ++;
            }
            
            $nomSequence = $nomSequence . ".json";
            
            $filesystem->dumpFile(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence, $fluxJson);
            
            // ////Enregistrement du fichier dans la bdd///////////////////////
            $sequenceBdd = new Sequence();
            $url = $pathcourant . DIRECTORY_SEPARATOR . DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence;
            // $url = $_SERVER['DOCUMENT_ROOT'].$nomSequence; //url de la séquence enregistrée
            $dateJour = new \DateTime();
            dump($dateJour);
            $sequenceBdd->setUrl($url);
            $sequenceBdd->setDate($dateJour);
            
            $manager->persist($sequenceBdd);
            $manager->flush();
            $this->addFlash("success", "La séquence " . $nomSequence . " a bien été enregistrée");
            return $this->redirectToRoute('sequence_selectionnerSequence');
        }
        return $this->redirectToRoute('sequence_selectionnerSequence');
    }*/
    
    /**
     *
     * @Route("/accueil", name="sequence_accueil")
     */
    public function accueilSequence()
    {
        return $this->render('sequence/accueilSequence.html.twig', [
            'controller_name' => 'SequenceController'
        ]);
    }
    
    /**
     *
     * @Route("/sequence/afficher/{nomFichierSequence}", name="sequence_afficher")
     */
    public function afficher($nomFichierSequence)
    {
        $sequence = "";
        // dd($nomFichierSequence);
        $file = DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomFichierSequence;
        if (file_exists($file)) {
            $sequence = file_get_contents($file);
            //dd($sequence);
        }
        return $this->render('sequence/afficher.html.twig', [
            'controller_name' => 'SequenceController',
            'sequence' => $sequence,
            'nomSequence'=>$nomFichierSequence
        ]);
    }
    
    /**
     *
     * @Route("/sequence/selectionnerSequence", name="sequence_selectionnerSequence")
     */
    public function selectionnerSequence(SequenceRepository $repo)
    {
        $sequence = $repo->findAll();
        
        return $this->render('sequence/formulaireSequence.html.twig', [
            'controller_name' => 'SequenceController',
            'sequence' => $sequence
        ]);
    }
    
    /**
     *
     * @Route("/sequence/TraitementSequence", name="sequence_traitementSequence")
     */
    public function traitement(EntityManagerInterface $manager, SequenceRepository $repo)
    {
        if (isset($_POST["afficher"])) {
            // if (isset($_POST["cocher"]) ) // si on a appuyé sur "afficher"
            // {
            foreach ($_POST['cocher'] as $sequence) {
                
                return $this->redirectToRoute('sequence_afficher', [ // on est redirigé vers la route "sequence_afficher" qui va afficher la séquence que l'on a sélectionnée
                    "nomFichierSequence" => $sequence
                ]);
            }
        }
        if (isset($_POST["renommer"])) {
            // if (isset($_POST["cocher"]) ) // si on a appuyé sur "afficher"
            // {
            foreach ($_POST['cocher'] as $sequence) {
                
                return $this->redirectToRoute('sequence_rename', [
                    "nomFichierSequence" => $sequence
                ]);
            }
        }
        if (isset($_POST["supprimer"])) {
            if (isset($_POST["cocher"])) {
                foreach ($_POST['cocher'] as $sequence) {
                    $this->suppressionFichier($sequence, $manager, $repo);
                }
                return $this->redirectToRoute('sequence_selectionnerSequence');
            }
        }
    }
    
    
    /**
     *
     * @Route("/sequence/suppression/{nomFichierSequence}", name="sequence_suppression")
     */
    public function suppressionFichier($nomFichierSequence, EntityManagerInterface $manager, SequenceRepository $repo)
    {
        $pathcourant = getcwd();
        $url = $pathcourant . DIRECTORY_SEPARATOR . DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomFichierSequence;
        // $url = $_SERVER['DOCUMENT_ROOT'].$nomFichierSequence; //url de la séquence enregistrée en base de données
        unlink(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomFichierSequence); // suppression du fichier
        $sequence = new Sequence();
        $sequence = $repo->findOneBy(array(
            "url" => $url
        )); // recherche dans la base de données l'enregistrement qui a l'url "$url"
        $manager->remove($sequence);
        $manager->flush();
        $this->addFlash("success", "La séquence " . $nomFichierSequence . " a bien été supprimée");
        return $this->redirectToRoute('sequence_selectionnerSequence');
    }
}
