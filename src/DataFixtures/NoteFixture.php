<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Note;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NoteFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 100; ++$i) {
            $note = new Note();

            $note
                ->setTitle('Note '.$i)
                ->setText('Text for note '.$i)
                ->setStartDate(new \DateTime('2019-01-01'))
                ->setEndDate(new \DateTime('2021-11-30'))
            ;

            $manager->persist($note);
        }

        $manager->flush();
    }
}
