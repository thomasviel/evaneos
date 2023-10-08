<?php


require_once __DIR__ . '/../src/Entity/Destination.php';
require_once __DIR__ . '/../src/Entity/Quote.php';
require_once __DIR__ . '/../src/Entity/Site.php';
require_once __DIR__ . '/../src/Entity/Template.php';
require_once __DIR__ . '/../src/Entity/User.php';
require_once __DIR__ . '/../src/Helper/SingletonTrait.php';
require_once __DIR__ . '/../src/Context/ApplicationContext.php';
require_once __DIR__ . '/../src/Repository/Repository.php';
require_once __DIR__ . '/../src/Repository/DestinationRepository.php';
require_once __DIR__ . '/../src/Repository/QuoteRepository.php';
require_once __DIR__ . '/../src/Repository/SiteRepository.php';
require_once __DIR__ . '/../src/TemplateManager.php';

class TemplateManagerTest extends \PHPUnit\Framework\TestCase
{
    private \Faker\Generator $faker;
    private TemplateManager $templateManager;
    private Destination $expectedDestination;
    private Site $expectedSite;
    private User $currentUser;


    protected function setUp(): void
    {
        $this->faker = \Faker\Factory::create();
        $this->templateManager = new TemplateManager();
        $this->expectedDestination = DestinationRepository::getInstance()->getById($this->faker->randomNumber());
        $this->expectedSite = SiteRepository::getInstance()->getById($this->faker->randomNumber());
        $this->currentUser = ApplicationContext::getInstance()->getCurrentUser();
    }

    /**
     * @test
     */
    public function testDestinationName()
    {
        $quote = new Quote($this->faker->randomNumber(), $this->faker->randomNumber(), $this->expectedDestination->id, $this->faker->date());
        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            "Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name]"
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $this->expectedDestination->countryName, $message->subject);
        $this->assertEquals("Merci d'avoir contacté un agent local pour votre voyage " . $this->expectedDestination->countryName, $message->content);
    }

    /**
     * @test
     */
    public function testDestinationLink()
    {
        $quote = new Quote($this->faker->randomNumber(), $this->expectedSite->id, $this->expectedDestination->id, $this->faker->date());

        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            'Vous pouvez accéder à votre voyage sur le site [quote:destination_link]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $this->expectedDestination->countryName, $message->subject);
        $this->assertEquals('Vous pouvez accéder à votre voyage sur le site ' . $this->expectedSite->url . '/' . $this->expectedDestination->countryName . '/quote/' . $quote->id . '', $message->content);
    }

    /**
     * @test
     */
    public function testSummary()
    {
        $quote = new Quote($this->faker->randomNumber(), $this->faker->randomNumber(), $this->expectedDestination->id, $this->faker->date());

        $template = new Template(
            1,
            'Summary subject : [quote:summary]',
            'Summary content : [quote:summary]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Summary subject : ' . $quote->id, $message->subject);
        $this->assertEquals('Summary content : ' . $quote->id, $message->content);
    }

    /**
     * @test
     */
    public function testSummaryHTML()
    {
        $quote = new Quote($this->faker->randomNumber(), $this->faker->randomNumber(), $this->expectedDestination->id, $this->faker->date());

        $template = new Template(
            1,
            'Summary subject : [quote:summary_html]',
            'Summary content : [quote:summary_html]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Summary subject : <p>' . $quote->id . '</p>', $message->subject);
        $this->assertEquals('Summary content : <p>' . $quote->id . '</p>', $message->content);
    }

    /**
     * @test
     */
    public function testApplicationContextUser()
    {
        $quote = new Quote($this->faker->randomNumber(), $this->expectedSite->id, $this->expectedDestination->id, $this->faker->date());

        $template = new Template(
            1,
            '[user:first_name], votre voyage est prêt',
            'Bonjour [user:first_name]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals($this->currentUser->firstname . ', votre voyage est prêt', $message->subject);
        $this->assertEquals('Bonjour ' . $this->currentUser->firstname, $message->content);
    }

    /**
     * @test
     */
    public function testSpecificUser()
    {
        $quote = new Quote($this->faker->randomNumber(), $this->expectedSite->id, $this->expectedDestination->id, $this->faker->date());
        $user = new User($this->faker->randomNumber(), $this->faker->firstName, $this->faker->lastName, $this->faker->email);

        $template = new Template(
            1,
            '[user:first_name], votre voyage est prêt',
            'Bonjour [user:first_name]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote,
                'user' => $user
            ]
        );

        $this->assertEquals($user->firstname . ', votre voyage est prêt', $message->subject);
        $this->assertEquals('Bonjour ' . $user->firstname, $message->content);
    }
    /**
     * @test
     */
    public function testWithoutQuoteData()
    {
        $user = new User($this->faker->randomNumber(), $this->faker->firstName, $this->faker->lastName, $this->faker->email);

        $template = new Template(
            1,
            '[user:first_name], votre voyage est prêt',
            'Bonjour [user:first_name]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'user' => $user
            ]
        );

        $this->assertEquals($user->firstname . ', votre voyage est prêt', $message->subject);
        $this->assertEquals('Bonjour ' . $user->firstname, $message->content);
    }
}
