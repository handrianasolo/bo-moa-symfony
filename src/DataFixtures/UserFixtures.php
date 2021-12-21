<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private EntityManagerInterface $manager;
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->manager = $manager;        
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        // Create users
        $this->createUser('U773429', 'atrad@groupe-casino.fr', 'admin', ['ROLE_SUPADMIN', 'ROLE_ADMIN'], 'TRAD', 'AHMED YAHIA');
        $this->createUser('U817182', 'handrianasolo@groupe-casino.fr', 'admin', ['ROLE_SUPADMIN', 'ROLE_ADMIN'], 'ANDRIANASOLO', 'HANITRA');
        $this->createUser('U195700', 'maboubi@groupe-casino.fr', 'admin', ['ROLE_ADMIN'], 'ABOUBI', 'MUSTAPHA');
        $this->createUser('U347050', 'cfiori@groupe-casino.fr', 'admin', ['ROLE_ADMIN'], 'FIORI', 'CHRISTIAN');
        $this->createUser('U281511', 'jmaurin@groupe-casino.fr', 'admin', ['ROLE_ADMIN'], 'MAURIN', 'JEROME');

        $manager->flush();
    }

    protected function createUser(string $id, string $email, string $password, array $roles = [], string $lastname, string $firstname): User
    {
        $user = new User();
        $user->setId($id)
            ->setEmail($email)
            ->setPassword(
                $this->passwordEncoder->hashPassword($user, $password)
            )
            ->setRoles($roles)
            ->setLastname($lastname)
            ->setFirstname($firstname);
        $this->manager->persist($user);
        return $user;
    }
}
