namespace Sp\FixtureDumper\Tests\Generator\Fixture;

use Sp\FixtureDumper\Tests\Fixture\Author;

/**
 * This code was generated automatically by the DataFixtures library, manual changes to it
 * may be lost upon next generation.
 */
class LoadAuthorData extends \Doctrine\Common\DataFixtures\AbstractFixture
{
    public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        $author2 = new Author();
        $author2->setUsername('Username');
        $manager->persist($author2);
        $this->addReference('author2', $author2);

        $manager->flush();
    }
}