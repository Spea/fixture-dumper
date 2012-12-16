namespace Sp\FixtureDumper\Tests\Generator\Fixture;

use Sp\FixtureDumper\Tests\Fixture\Post;

/**
 * This code was generated automatically by the FixtureDumper library, manual changes to it
 * may be lost upon next generation.
 */
class LoadPostData extends \Doctrine\Common\DataFixtures\AbstractFixture implements \Doctrine\Common\DataFixtures\DependentFixtureInterface
{
    public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        $post10 = new Post();
        $post10->setDescription('Description');
        $post10->setTitle('Title');
        $post10->setCreated(unserialize('O:8:"DateTime":3:{s:4:"date";s:19:"2012-12-12 12:12:12";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/Berlin";}'));
        $post10->setAuthor($this->getReference('author2'));
        $manager->persist($post10);
        $this->addReference('post10', $post10);

        $post11 = new Post();
        $post11->setDescription('Description2');
        $post11->setTitle('Title2');
        $post11->setCreated(unserialize('O:8:"DateTime":3:{s:4:"date";s:19:"2012-12-12 12:12:12";s:13:"timezone_type";i:3;s:8:"timezone";s:13:"Europe/Berlin";}'));
        $post11->setAuthor($this->getReference('author2'));
        $manager->persist($post11);
        $this->addReference('post11', $post11);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Sp\FixtureDumper\Tests\Generator\Fixture\LoadAuthorData'
        );
    }
}