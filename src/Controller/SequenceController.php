<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Sequence;
use App\Repository\SequenceRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


const DOSSIER_SEQUENCES = "sequences";  //Déclaration d'une constante

class SequenceController extends AbstractController
{
    /**
     *
     * @Route("accueil", name="sequence_accueil")
     */
    public function accueilSequence()
    {
        return $this->render('sequence/accueilSequence.html.twig', [
            'controller_name' => 'SequenceController'
        ]);
    }
    
    private function VerifNom($nom)
    {
        
        if (empty($nom)) {
            $nom = "sequence";
        }
        
        $nom = str_replace(' ', '', $nom); //https://www.php.net/manual/fr/function.str-replace.php : permet de remplacer une valeur par une autre
        $tailleSequence = strlen($nom);//https://www.php.net/manual/fr/function.strlen.php : calcule la taille de la chaine $nom
        $nomSequence = substr($nom, 0, $tailleSequence);//https://www.php.net/manual/fr/function.substr.php : retourne la partie souhaitée de la chaine $nom
        
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
        $nom1=explode(",", $nom);   //https://www.php.net/manual/fr/function.explode.php : convertit la chaine $nom en tableau avec comme séparateur la virgule
        $tab=[];
        
        for($j=0;$j<count($nom1);$j=$j+6)
        {
            for($i=0;$i<6;$i++)
            {
                
                array_push($tab,array("Angle".($i+1) => $nom1[$j+$i])); //https://www.php.net/manual/fr/function.array-push.php : empile les valeurs d'angles a la fin du tableau $tab
                //array_push($tab,array("Angle".($i+1) => $nom1[$j+$i]));
            }
            
        }
        
        $tab1=array_chunk($tab, 6);     //https://www.php.net/manual/fr/function.array-chunk.php : sépare le tableau $tab en plusieurs tableaux ayant chacun 6 valeurs
        return json_encode($tab1);
        
    }
    
    
    
   
    /**
     *
     * @Route("/sequence/save/", name="sequence_save")
     */
    /*public function SequenceSave()
    {
        return $this->render('sequence/save.html.twig', [
            'controller_name' => 'SequenceController'
        ]);
    }
    */
    /**
     *
     * @Route("/sequence/rename/{nomFichierSequence}", name="sequence_rename")
     */
    public function rename($nomFichierSequence, Request $request, SequenceRepository $repo, EntityManagerInterface $manager)
    {
        $pathcourant = getcwd();    //https://www.php.net/manual/fr/function.getcwd.php : Retourne le dossier de travail courant
        // création du formulaire avec les différents champs leurs types, leurs contraintes et attributs , et le submit
        $form = $this->createFormBuilder()      // création du formulaire
        ->add('nom', TextType::class, [
            'required' => false,
            'data' => substr($nomFichierSequence, 0, - 5) //https://www.php.net/manual/fr/function.substr.php : on retourne la partie voulue de la chaine $nomFichierSequence
        ])
        ->add('Valider', SubmitType::class)
        ->getForm();
        // on demande au formulaire d'analyser les infos de la requête et de récupérer les saisies en cas de submit
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData(); // retourne les données du formulaire dans un tableau
            $nom=$donnees['nom'];// Récupère les données du champ 'nom'
            $nomSequence=$this->VerifNom($nom);// appelle la fonction VerifNom()
            
            rename(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomFichierSequence, DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence); //https://www.php.net/manual/fr/function.rename.php : permet de renommer le fichier $nomFichierSequence en $nomSequence
            // ////Enregistrement du fichier dans la bdd///////////////////////
            $sequenceBdd = $repo->findOneURLByEnd($nomFichierSequence);
            
            $url = $pathcourant . DIRECTORY_SEPARATOR . DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence; //l'url du fichier
            
            $sequenceBdd->setUrl($url); //envoie de l'url dans la table "sequence"
            
            $manager->persist($sequenceBdd);
            $manager->flush();
            $this->addFlash("success", "La séquence " . " \"".$nomFichierSequence. "\" a été renommée en " . " \"".$nomSequence. "\"");
            return $this->redirectToRoute('sequence_selectionnerSequence');
        }
        return $this->render('sequence/sequenceRenommer.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    
    /**
     *
     * @Route("/sequence/enregistrerSequence/{nomFichierSequence}", name="sequence_enregistrerSequence")
     */
    public function enregistrer($nomFichierSequence=null, Request $request, SequenceRepository $repo, EntityManagerInterface $manager)
    {
        //$nomSequence = $nomFichierSequence;
        $filesystem = new Filesystem(); //https://symfony.com/doc/current/components/filesystem.html : instanciation d'un nouvel objet Filesystem
        $pathcourant = getcwd();    //https://www.php.net/manual/fr/function.getcwd.php : Retourne le dossier de travail courant
        $filesystem->mkdir(DOSSIER_SEQUENCES, 0777);    //création du répertoire "sequences" dans public si il n'est pas déjà créé.
        // création du formulaire avec les différents champs leurs types, leurs contraintes et attributs , et le submit
        $form = $this->createFormBuilder() // création du formulaire
        ->add('nom', TextType::class, [
            'required' => false,
            
        ]
            
           )
        ->add('data', HiddenType::class)
        ->getForm();
        // on demande au formulaire d'analyser les infos de la requête et de récupérer les saisies en cas de submit
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData(); // retourne les données du formulaire dans un tableau
            
            $nomSequence = $donnees['nom']; // Récupère les données du champ 'nom'
            $flux = $donnees['data'];   // Récupère les données du champ 'data'
            
            $nomSequence=$this->VerifNom($nomSequence); //appel de la fonction VerifNom()
            $fluxJson=$this->fluxJson($flux);           //appel de la fonction fluxJson()
            
            $filesystem->dumpFile(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence, $fluxJson);   //met le contenu de la variable $fluxJson dans la variable $nomSequence
            // ////Enregistrement du fichier dans la bdd///////////////////////
            $sequenceBdd = new Sequence();      //Instanciation d'un nouvel objet de la classe Sequence
            
            $url = $pathcourant . DIRECTORY_SEPARATOR . DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence; //l'url du ficiher $nomSequence
            
            date_default_timezone_set('Europe/Paris');
            $dateJour = new \DateTime();
            $sequenceBdd->setUrl($url);
            
            
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
     * @Route("/sequence/afficher/{nomFichierSequence}", name="sequence_afficher")
     */
    public function afficher($nomFichierSequence)
    {
        $sequence = "";
        // dd($nomFichierSequence);
        $file = DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomFichierSequence;  //endroit où est le fichier $nomFichierSequence
        if (file_exists($file)) {
            $sequence = file_get_contents($file);   //récupération du contenu de la variable $file pour le mettre dans la variable $sequence
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
        $pathcourant = getcwd();    //https://www.php.net/manual/fr/function.getcwd.php : Retourne le dossier de travail courant
        $url = $pathcourant . DIRECTORY_SEPARATOR . DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomFichierSequence;  //url du fichier $nomFichierSequence
        // $url = $_SERVER['DOCUMENT_ROOT'].$nomFichierSequence; //url de la séquence enregistrée en base de données
        unlink(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomFichierSequence); // https://www.php.net/manual/fr/function.unlink.php : suppression du fichier
        $sequence = new Sequence(); //Instanciation d'un nouvel objet de la classe Sequence
        $sequence = $repo->findOneBy(array(
            "url" => $url
        )); // recherche dans la base de données l'enregistrement qui a l'url "$url"
        $manager->remove($sequence);
        $manager->flush();
        $this->addFlash("success", "La séquence " . " \"".$nomFichierSequence. "\" a bien été supprimée");
        return $this->redirectToRoute('sequence_selectionnerSequence');
    }
}
