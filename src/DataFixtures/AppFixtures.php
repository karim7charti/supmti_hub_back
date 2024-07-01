<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private  UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

       /* for($i=0;$i<100;$i++)
        {
            $user=new User();
            $user->setFirstName($faker->firstNameMale());
            $user->setLastName($faker->lastName());
            $user->setEmail($faker->email());
            $password=$faker->password(maxLength: 12);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $password
            );
            $user->setPassword($hashedPassword);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setRoles(["ROLE_ETUDIANT"]);
            $manager->persist($user);

        }*/


        /*
         *
         * hna min tabghy t insrer admin  gl3 lhady cmntr o commenty li lfo9
         * o lanci php bin/console doctrine:fixtures:load --append wdir data t3k li t5rjk ak tama
        $user=new User();
        $user->setFirstName("mohamed");
        $user->setLastName("mhada");
        $user->setEmail("shzklld@gmail.com");
        //$password=$faker->password(maxLength: 12);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            "mohamedmhada34"
        );
        $user->setPassword($hashedPassword);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);*/


        $manager->flush();
    }
}
