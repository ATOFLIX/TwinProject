<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AccountType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Form\RegistrationType;
use App\Form\AdminType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    /**
     * @Route("/account", name="account")
     */
    public function index($id = null, Request $request, EntityManagerInterface $em, UserRepository $tRep, PaginatorInterface $paginator, 
                            UserPasswordEncoderInterface $encoder)
    {
        $q = $request->query->get('q');
        $pagination = null;
    	if($id == null)
        {
            $user = new user;
        }else
        {
            $user = $tRep->findOneBy(['id' => $id]);
        }

        $form=$this->createForm(AccountType::class);                            //Création d'un formulaire depuis le fichier AccountType.php     
        $form->handleRequest($request);                                         //Recuperer les requête du formulaire (au moment ou on appuie sur le bouton valider ou la touche entrée).
        if($form->isSubmitted() && $form->isValid()) {                          //Si le form est valide et les champs sont remplis
            //dd($form)          
            if($form->getData()["username"])                                    //si c'est le champs username on fait une recherche par username grace au UserRepository $er
            {
                $this->session->set('type', 'username');
                $AllUsername = '%'.$form->getData()['username'].'%';
                $this->session->set('value', $AllUsername);
            }
            elseif($form->getData()["nom"])
            {
                $this->session->set('type', 'nom');
                $AllNom = '%'.$form->getData()['nom'].'%';
                $this->session->set('value', $AllNom);
            }
            elseif($form->getData()["prenom"])
            {
                $this->session->set('type', 'prenom');
                $AllPrenom = '%'.$form->getData()['prenom'].'%';
                $this->session->set('value', $AllPrenom);
            }
            // il faudra faire de même quand il y aura les noms et prénoms des users
            else
            {
                $this->session->set('type', 'all');
            }  
            return $this->redirectToRoute('account');
        }
        $type = $this->session->get('type');
        $value = $this->session->get('value');    
        /*if($type == 'all')
        {
            $queryBuilder = $tRep->Order($q);
        }else
        {
            $queryBuilder = $tRep->getAllByTerm($q, $type, $value);
        }*/

        $queryBuilder = $tRep->Order($q);

        $formNewUser = $this->createForm(RegistrationType::class, $user);
        $formNewUser->handleRequest($request);

        if($formNewUser->isSubmitted() && $formNewUser->isValid())
        {
           /* dd($user);*/
           $hash = $encoder->encodePassword($user, $user->getPassword());
            
           $user->setPassword($hash);
           $user->setRoles("ROLE_USER");
           $em->persist($user);
           $em->flush();
        }

        /*$formNewAdmin = $this->createForm(AdminType::class);
        $formNewAdmin->handleRequest($request);

        if($formNewAdmin->isSubmitted() && $formNewAdmin->isValid())
        {
            if ($user->getRoles() == "ROLE_USER"){
                $user->getRoles() == true;
            }
            else {
                $user->getRoles() ==  false;
            }
            $em->persist($user);
            $em->flush();
        }*/

        $pagination = $paginator->paginate ($queryBuilder, $request->query->getInt('page', 1), 20);

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
            'formNewUser' => $formNewUser->createView(),
            //'formNewAdmin' => $formNewAdmin->createView(),
            'pagination' => $pagination,
            'id' => $id,
        ]);
    }

    
    /**
     * @Route("/account/{id}", name="account_edit")
     */
    public function edit(User $user) {

        $form = $this->createForm(UserType::class,$user);
        return $this->render('account/editUser.html.twig',[
            "user" => $user,
            "form" => $form->createView()
        ]);
    }


     /**
     * @Route("/account/delete/{id}", name="account_del")
     * 
     */
    public function delete($id) {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(user::class)->find($id);
        if(!$user)
        {
            throw $this->createNotFoundException(
                'Plus de compte avec pour id : '  . $id
            );
        }
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('account');
    }   
}
