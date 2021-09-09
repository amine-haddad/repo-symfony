<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Entity\Season;
use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugger;
    public function __construct(Slugify $slugger)
    {
        $this->slugger = $slugger;
    }
    const EPISODES = ['Episode 1', 'Episode 2', 'Episode 3', 'Episode 4', 'Episode 5', 'Episode 6', 'Episode 7', 'Episode 8'];
    function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        foreach (ProgramFixtures::PROGRAMS as $programId => $program) {
            //var_dump($program);//die();
            foreach (SeasonFixtures::SEASONS as $seasonId => $season) {
                //var_dump($season);
                foreach (self::EPISODES as $episodeId => $episodes) {
                    //var_dump($episodes);
                    $episode = new Episode();
                    if ($this->hasReference('season_' . $programId . $seasonId)) {
                        $episode->setSeason($this->getReference('season_' . $programId . $seasonId));
                    }
                    //$episode->setSeason($this->getReference('season_' . $seasonId));
                    $episode->setTitle($episodes);
                    $slug = $this->slugger->generate($episode->getTitle());
                    $episode->setSlug($slug);
                    $episode->setNumber($episodeId);
                    $episode->setSynopsis('synopsys' . $episodeId);
                    $manager->persist($episode);
                }
            }
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            SeasonFixtures::class,
            ProgramFixtures::class
        ];
    }
}
