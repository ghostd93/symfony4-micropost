<?php

namespace App\DataFixtures;

use \Faker\Factory;
use App\Entity\User;
use App\Entity\MicroPost;
use App\Entity\UserPreferences;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $faker;
    private $userPasswordEncoder;

    private const LANGUAGES = [
        'en',
        'pl'
    ];

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->faker = Factory::create();
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadMicroPosts($manager);
    }

    private function loadMicroPosts(ObjectManager $manager)
    {
        foreach ($this->referenceRepository->getReferences() as $user) {
            $microPost = new MicroPost();
            $microPost->setTime($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $microPost->setText($this->faker->text);
            $microPost->setUser($user);
            $manager->persist($microPost);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        for($i = 0; $i < 10; $i++){
            $user = new User();
            $user->setUsername($this->faker->userName);
            $user->setFullName($this->faker->name);
            $user->setEmail($this->faker->email);
            $user->setEnabled(true);
            $user->setPassword(
                $this->userPasswordEncoder->encodePassword(
                    $user,
                    '12345678'
                )
            );
            $user->setRoles(['ROLE_USER']);

            $this->addReference($user->getUsername(), $user);
            $preferences = new UserPreferences();
            $preferences->setLocale(self::LANGUAGES[rand(0, 1)]);
            $user->setPreferences($preferences);
            $manager->persist($user);
        }

        $user = new User();
        $user->setUsername('admin');
        $user->setFullName('Super Admin');
        $user->setEmail('admin@admin.com');
        $user->setEnabled(true);
        $user->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $user,
                '12345678'
            )
        );
        $user->setRoles(['ROLE_ADMIN']);
        $preferences = new UserPreferences();
        $preferences->setLocale(self::LANGUAGES[rand(0, 1)]);
        $user->setPreferences($preferences);
        $manager->persist($user);

        $manager->flush();
    }
}
