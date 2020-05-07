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
use Symfony\Component\Form\Extension\Core\Type\ResetType;

const DOSSIER_SEQUENCES = "sequences";

class SequenceController extends AbstractController
{

    /**
     *
     * @Route("/sequence/afficher/{nomFichierSequence}", name="sequence_afficher")
     */
    public function afficher($nomFichierSequence)
    {   $sequence="";
       // dd($nomFichierSequence);
        $file = DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomFichierSequence;
        if (file_exists($file)) {
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
    /*public function selectionner() // sélection des séquences pour les afficher et les supprimer
    {
        $listesFichiers = array(); // création d'un tableau vide
        $tab = scandir(DOSSIER_SEQUENCES); // Scan du répertoire où est enregistrée la séquence
        for ($i = 0; $i < count($tab); $i ++) {
            if (substr($tab[$i], 0, 1) != ".") {
                array_push($listesFichiers, $tab[$i]);
            }
        }

        return $this->render('sequence/formulaire.html.twig', [ // Renvoie vers la page "formulaire.html.twig"
            'controller_name' => 'SequenceController',
            'listeFichiers' => $listesFichiers
        ]);
    }
*/
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
        if (isset($_POST["supprimer"])) {
            if (isset($_POST["cocher"])) // si on a appuyé sur "afficher"
            {
                foreach ($_POST['cocher'] as $sequence) {
                    $this->suppressionFichier($sequence, $manager, $repo);
                }
                return $this->redirectToRoute('sequence_selectionnerSequence');
            }
        }
    }

    /**
     *
     * @Route("/sequence/Traitement", name="sequence_traitement")
     */
    /*public function traitementFormulaire() // Traitement du formulaire pour supprimer et afficher les séquences
    {
        $sequence = $_POST["selection"]; // on récupère la séquence que l'on a sélectionné dans le formulaire
        if (isset($_POST["afficher"])) // si on a appuyé sur "afficher"
        {
            return $this->redirectToRoute('sequence_afficher', [ // on est redirigé vers la route "sequence_afficher" qui va afficher la séquence que l'on a sélectionnée
                "nomFichierSequence" => $sequence
            ]);
        } else if (isset($_POST["supprimer"])) // sinon si on a appuyé sur "supprimer"
        {
            return $this->redirectToRoute('sequence_suppression', [ // on va être redirigé vers la route "sequence_suppression"
                "nomFichierSequence" => $sequence
            ]);
        } else if (isset($_POST["renommer"])) {
            return $this->redirectToRoute('sequence_traitementNom', [
                "nomFichierSequence" => $sequence
            ]);
        }
    }*/

    /**
     *
     * @Route("/sequence/TraitementRenommer/{$nomFichierSequence}", name="sequence_traitementRenommer")
     */
    /*public function traitementRenommer($nomFichierSequence, EntityManagerInterface $manager, SequenceRepository $repo)
    {
        $nomSequence = $manager->refresh($nomFichierSequence);
        if (isset($_POST["oui"])) {

            return $this->redirectToRoute('sequence_traitementNom', [
                "nomFichierSequence" => $nomSequence
            ]);
        } else if (isset($_POST["non"])) {
            return $this->render('sequence/enregistrer.html.twig', [
                'controller_name' => 'SequenceController'
            ]);
        }
    }*/

    /**
     *
     * @Route("/sequence/TraitementNom", name="sequence_traitementNom")
     */
    public function traitementNomSequence(Request $request, EntityManagerInterface $manager)
    {
        $nomSequence = "";
        $filesystem = new Filesystem();
        $pathcourant = getcwd();
        $filesystem->mkdir(DOSSIER_SEQUENCES, 0777);

        $file = $pathcourant . DIRECTORY_SEPARATOR . "fichier.txt";

        $form = $this->createFormBuilder()
            ->add('nomSequence', TextType::class, [
            'required' => false
        ])
            ->add('OK', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();
            $nomSequence = $donnees['nomSequence'];
            //$nomSequence=trim($nomSequence);
            $nomSequence=str_replace(' ', '', $nomSequence);
            if (! $nomSequence) {
                $nomSequence = "sequence";
            }
            $tailleSequence = strlen($nomSequence);

            $nom = substr($nomSequence, 0, $tailleSequence);

            if ($filesystem->exists($file)) {
                $i = 1;
                $sequence = file_get_contents($file);

                while (file_exists(DOSSIER_SEQUENCES . DIRECTORY_SEPARATOR . $nomSequence)) {

                    $nomSequence = $nom . $i;
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

                /*
                 * return $this->render('sequence/enregistrer.html.twig', [
                 * 'controller_name' => 'SequenceController',
                 * 'nomSequence' => $nomSequence,
                 * 'form' => $form->createView()
                 * ]);
                 */
            }
        }

        return $this->render('sequence/enregistrer.html.twig', [
            'controller_name' => 'SequenceController',
            'form' => $form->createView(),
            'nomSequence' => $nomSequence
        ]);
    }

    /**
     *
     * @Route("/sequence/selectionNom", name="sequence_selectionNom")
     */
    /*
     * public function selectionSequence(Request $request, EntityManagerInterface $manager, SequenceRepository $repo)
     * {
     * //$filesystem = new Filesystem();
     *
     * $form=$this->createFormBuilder()->add('selection',TextType::class, ['required'=> false ])
     * ->add('afficher',SubmitType::class)
     * ->add('supprimer',SubmitType::class)
     * ->add('annuler',ResetType::class)
     * ->getForm();
     * $form->handleRequest($request);
     *
     * $sequence=$repo->findAll();
     *
     * if($form->isSubmitted() && $form->isValid())
     * {
     * return $this->render('sequence/formulaire.html.twig', [
     * 'controller_name' => 'SequenceController',
     * 'form' => $form->createView(),
     * 'nomSequence'=>$Sequence,
     * ]);
     * }
     *
     * return $this->render('sequence/formulaire.html.twig', [
     * 'controller_name' => 'SequenceController',
     * 'form' => $form->createView(),
     * 'nomSequence'=>$Sequence,
     * ]);
     * }
     */

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
        return $this->redirectToRoute('sequence_selectionnerSequence');
    }
}
