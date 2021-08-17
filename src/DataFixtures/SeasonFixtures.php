<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    const SEASONS = ['saison 1', 'saison 2', 'saison 3', 'saison 4'];
    public function load(ObjectManager $manager)
    {
        for ($j = 0; $j < count(ProgramFixtures::PROGRAMS); $j++) {
            foreach (self::SEASONS as $seasonId => $seasonsTitle) {
                $season = new Season();
                $season->setProgram($this->getReference('program_' . $j));
                $season->setYear('2001');
                $season->setNumber($seasonId);
                $season->setDescription($seasonsTitle);
                $manager->persist($season);
                $this->addReference('season_' .$j. $seasonId, $season);
            }
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
