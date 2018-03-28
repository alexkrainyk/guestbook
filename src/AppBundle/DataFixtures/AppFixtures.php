<?php
/**
 * User: alex
 * Date: 3/27/18
 * Time: 10:59 AM
 */

namespace AppBundle\DataFixtures;


use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $client = new User();
        $client->setName('Test client');
        $client->setEmail('test.client@test.com');
        $client->setRegisteredAt(new \DateTime());
        $client->setRoles(['ROLE_CLIENT']);

        $password = $this->encoder->encodePassword($client, 'test1234');
        $client->setPassword($password);

        $userManager = new User();
        $userManager->setName('Test manager');
        $userManager->setEmail('test.manager@test.com');
        $userManager->setRegisteredAt(new \DateTime());
        $userManager->setRoles(['ROLE_MANAGER']);

        $password = $this->encoder->encodePassword($userManager, 'test1234');
        $userManager->setPassword($password);

        $manager->persist($client);
        $manager->persist($userManager);
        $manager->flush();
    }
}