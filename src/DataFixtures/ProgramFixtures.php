<?php

namespace App\DataFixtures;

use App\Entity\Program;
use App\Entity\User;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugger;
    public function __construct(Slugify $slugger)
    {
        $this->slugger = $slugger;
    }
    const PROGRAMS = ['Film 1','Film 2','Film 3','Film 4','Film 5','Film 6'];
    public function load(ObjectManager $manager)
    {
        $faker= Faker\Factory::create('fr_FR');
        $faker->addProvider(new \Xvladqt\Faker\LoremFlickrProvider ( $faker ));
       
        foreach (self::PROGRAMS as $key => $programName) {
            $program = new Program();
            //for ($i = 0; $i < count(CategoryFixtures::CATEGORIES); $i++) {
                $program->setCategory($this->getReference('category_' . rand(0, count(CategoryFixtures::CATEGORIES)-1), $program));
            //}
            //ici les acteurs sont insérés via une boucle pour être DRY mais ce n'est pas obligatoire
            for ($i = 0; $i < count(ActorFixtures::ACTORS); $i++) {   
               $program->addActor($this->getReference('actor_' . rand(0, count(ActorFixtures::ACTORS)-1), $program));
            }
            $program->setTitle($faker->sentence(rand(3, 6)));
            $slug = $this->slugger->generate($program->getTitle());
            $program->setSlug($slug);
            $program->setSummary($faker->sentence());
            $program->setPoster($faker->imageUrl( 640, 480,['cinema']));
            $program->setOwner($this->getReference('user_' , $program));
            $manager->persist($program);
            $this->addReference('program_' . $key, $program);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [    
            ActorFixtures::class,
            CategoryFixtures::class,
            UserFixtures::class
        ];
    }
}
