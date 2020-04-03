<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Id\AssignedGenerator;
use App\Entity\User;
use Faker\Factory;
use Faker\Generator;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

class Account extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $NbCompte = 100;
        
        for ($i=0;$i<$NbCompte;$i++){
            //--------GENERATIONS DES INFOS-------------------//
            $prenom = $faker->firstName;
            $nom = $faker->LastName;
            $nomAcc = $this->ote_accent($nom);
            $prenomAcc = $this->ote_accent($prenom);
            $mail = lcfirst($nomAcc).'.'.lcfirst($prenomAcc).'@qqchose.com';
    //     	    $mail = $this->ote_accent($mail);
            $password = $faker->randomNumber(8,false);

            $username_nb = $faker->randomNumber(2,false);

            $username = lcfirst($nomAcc).lcfirst($prenomAcc).$username_nb;

            $role = $faker->numberBetween(0,1);
            $role = $role ? 'ROLE_ADMIN' : 'ROLE_USER'; 
            //--------DEFINITIONS DES INFOS-------------------//
            $user=new user();
            //$user->setId($i);
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setPassword($password);
            $user->setRoles($role);
            $user->setUsername($username);
            $user->setEmail($mail);
            $manager->persist($user);
            /*$metadata = $this->em->getClassMetaData(User::class);    		
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
            $metadata->setIdGenerator(new AssignedGenerator());*/
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    private function ote_accent($str){
        $table = array(
            'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
            'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
            'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
            'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
            'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
        );
        $ch = strtr($str,$table);
        return $ch;
    }
}
