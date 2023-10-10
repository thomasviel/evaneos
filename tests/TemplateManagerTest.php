<?php


require_once __DIR__ . '/../src/Entity/Destination.php';
require_once __DIR__ . '/../src/Entity/Quote.php';
require_once __DIR__ . '/../src/Entity/Site.php';
require_once __DIR__ . '/../src/Entity/Template.php';
require_once __DIR__ . '/../src/Entity/User.php';
require_once __DIR__ . '/../src/Context/ApplicationContext.php';
require_once __DIR__ . '/../src/Repository/Repository.php';
require_once __DIR__ . '/../src/Repository/DestinationRepository.php';
require_once __DIR__ . '/../src/Repository/QuoteRepository.php';
require_once __DIR__ . '/../src/Repository/SiteRepository.php';
require_once __DIR__ . '/../src/Service/Placeholder/PlaceholderFactory.php';
require_once __DIR__ . '/../src/Service/Placeholder/PlaceholderReplacer.php';
require_once __DIR__ . '/../src/Service/Placeholder/QuoteDestinationLinkReplacer.php';
require_once __DIR__ . '/../src/Service/Placeholder/QuoteDestinationNameReplacer.php';
require_once __DIR__ . '/../src/Service/Placeholder/QuoteSummaryHtmlReplacer.php';
require_once __DIR__ . '/../src/Service/Placeholder/QuoteSummaryReplacer.php';
require_once __DIR__ . '/../src/Service/Placeholder/UserFirstnameReplacer.php';
require_once __DIR__ . '/../src/TemplateManager.php';

class TemplateManagerTest extends \PHPUnit\Framework\TestCase
{
    private \Faker\Generator $faker;
    private TemplateManager $templateManager;
    private Destination $expectedDestination;
    private Site $expectedSite;
    private \PHPUnit\Framework\MockObject\MockObject $applicationContextMock;
    private QuoteRepository $quoteRepositoryMock;
    private SiteRepository $siteRepositoryMock;
    private DestinationRepository $destinationRepositoryMock;

    private Quote $quote;

    protected function setUp(): void
    {
        $this->faker = \Faker\Factory::create();

        $this->quoteRepositoryMock = $this->createMock(QuoteRepository::class);
        $this->siteRepositoryMock = $this->createMock(SiteRepository::class);
        $this->destinationRepositoryMock = $this->createMock(DestinationRepository::class);
        $this->applicationContextMock = $this->createMock(ApplicationContext::class);
    }

    /**
     * Init template manager for tests with Quote input.
     * @return void
     */
    private function initForTestsWithQuoteInput()
    {
        $quoteId = $this->faker->randomNumber();
        $destinationId = $this->faker->randomNumber();
        $siteId = $this->faker->randomNumber();

        $this->expectedDestination = new Destination(
            $destinationId,
            $this->faker->country,
            'en',
            $this->faker->slug()
        );
        $this->expectedSite = new Site($siteId, $this->faker->url);
        $this->quote = new Quote($quoteId, $siteId, $destinationId, $this->faker->date());

        $this->siteRepositoryMock->expects($this->once())->method('getById')->with($siteId)->willReturn($this->expectedSite);
        $this->destinationRepositoryMock->expects($this->once())->method('getById')->with($destinationId)->willReturn($this->expectedDestination);
        $this->quoteRepositoryMock->expects($this->once())->method('getById')->with($quoteId)->willReturn($this->quote);
        $this->templateManager = new TemplateManager($this->quoteRepositoryMock, $this->siteRepositoryMock, $this->destinationRepositoryMock, $this->applicationContextMock);
    }
    /**
     * @test
     */
    public function testDestinationName()
    {
        $this->initForTestsWithQuoteInput();
        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            "Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name]"
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $this->quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $this->expectedDestination->getCountryName(), $message->getSubject());
        $this->assertEquals("Merci d'avoir contacté un agent local pour votre voyage " . $this->expectedDestination->getCountryName(), $message->getContent());
    }

    /**
     * @test
     */
    public function testDestinationLink()
    {
        $this->initForTestsWithQuoteInput();
        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            'Vous pouvez accéder à votre voyage sur le site [quote:destination_link]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $this->quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $this->expectedDestination->getCountryName(), $message->getSubject());
        $this->assertEquals('Vous pouvez accéder à votre voyage sur le site ' . $this->expectedSite->getUrl() . '/' . $this->expectedDestination->getCountryName() . '/quote/' . $this->quote->getId() . '', $message->getContent());
    }

    /**
     * @test
     */
    public function testSummary(): void
    {
        $this->initForTestsWithQuoteInput();
        $template = new Template(
            1,
            'Summary subject : [quote:summary]',
            'Summary content : [quote:summary]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $this->quote
            ]
        );

        $this->assertEquals('Summary subject : ' . $this->quote->getId(), $message->getSubject());
        $this->assertEquals('Summary content : ' . $this->quote->getId(), $message->getContent());
    }

    /**
     * @test
     */
    public function testSummaryHTML()
    {
        $this->initForTestsWithQuoteInput();
        $template = new Template(
            1,
            'Summary subject : [quote:summary_html]',
            'Summary content : [quote:summary_html]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $this->quote
            ]
        );

        $this->assertEquals('Summary subject : <p>' . $this->quote->getId() . '</p>', $message->getSubject());
        $this->assertEquals('Summary content : <p>' . $this->quote->getId() . '</p>', $message->getContent());
    }

    /**
     * @test
     */
    public function testApplicationContextUser()
    {
        $this->initForTestsWithQuoteInput();
        $currentUser = new User($this->faker->randomNumber(), $this->faker->firstName, $this->faker->lastName, $this->faker->email);
        $this->applicationContextMock->expects($this->once())->method('getCurrentUser')->willReturn($currentUser);

        $template = new Template(
            1,
            '[user:first_name], votre voyage est prêt',
            'Bonjour [user:first_name]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $this->quote
            ]
        );

        $this->assertEquals($currentUser->getFirstname() . ', votre voyage est prêt', $message->getSubject());
        $this->assertEquals('Bonjour ' . $currentUser->getFirstname(), $message->getContent());
    }

    /**
     * @test
     */
    public function testSpecificUser()
    {
        $this->initForTestsWithQuoteInput();
        $user = new User($this->faker->randomNumber(), $this->faker->firstName, $this->faker->lastName, $this->faker->email);

        $template = new Template(
            1,
            '[user:first_name], votre voyage est prêt',
            'Bonjour [user:first_name]'
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $this->quote,
                'user' => $user
            ]
        );

        $this->assertEquals($user->getFirstname() . ', votre voyage est prêt', $message->getSubject());
        $this->assertEquals('Bonjour ' . $user->getFirstname(), $message->getContent());
    }

    /**
     * @test
     */
    public function testWithoutQuoteData()
    {
        $this->templateManager = new TemplateManager($this->quoteRepositoryMock, $this->siteRepositoryMock, $this->destinationRepositoryMock, $this->applicationContextMock);
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

        $this->assertEquals($user->getFirstname() . ', votre voyage est prêt', $message->getSubject());
        $this->assertEquals('Bonjour ' . $user->getFirstname(), $message->getContent());
    }


    /**
     * @test
     */
    public function testMultiReplacementForSamePlaceholder()
    {
        $this->initForTestsWithQuoteInput();
        $template = new Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name], oui avec une agence locale [quote:destination_name]',
            "Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name], je répète : pour votre voyage [quote:destination_name]"
        );
        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $this->quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $this->expectedDestination->getCountryName() . ', oui avec une agence locale ' . $this->expectedDestination->getCountryName(), $message->getSubject());
        $this->assertEquals("Merci d'avoir contacté un agent local pour votre voyage " . $this->expectedDestination->getCountryName() . ', je répète : pour votre voyage ' . $this->expectedDestination->getCountryName(), $message->getContent());

    }
}