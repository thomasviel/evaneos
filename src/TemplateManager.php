<?php

class TemplateManager
{
    private QuoteRepository $quoteRepository;
    private SiteRepository $siteRepository;
    private DestinationRepository $destinationRepository;
    private ApplicationContext $applicationContext;
    private PlaceholderFactory $placeholderFactory;

    public function __construct(
        QuoteRepository $quoteRepository = null,
        SiteRepository $siteRepository = null,
        DestinationRepository $destinationRepository = null,
        ApplicationContext $applicationContext = null,
        PlaceholderFactory $placeholderFactory = null
    ) {
        $this->quoteRepository = $quoteRepository ?? new QuoteRepository();
        $this->siteRepository = $siteRepository ?? new SiteRepository();
        $this->destinationRepository = $destinationRepository ?? new DestinationRepository();
        $this->applicationContext = $applicationContext ?? new ApplicationContext();
        $this->placeholderFactory = $placeholderFactory ?? new PlaceholderFactory();
    }


    /**
     * Get template computed
     *
     * @param  Template                  $tpl
     * @param  array<string, Quote|User> $data
     * @return Template
     */
    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;
        $user  = (isset($data['user']) and ($data['user'] instanceof User)) ? $data['user'] : $this->applicationContext->getCurrentUser();

        $existingQuote = $quote ? $this->quoteRepository->getById($quote->getId()) : null;
        $existingSite = $existingQuote ? $this->siteRepository->getById($existingQuote->getSiteId()) : null;
        $existingDestination = $existingQuote ? $this->destinationRepository->getById($existingQuote->getDestinationId()) : null;

        return new Template(
            $tpl->getId(),
            $this->computeText($tpl->getSubject(), $user, $existingQuote, $existingSite, $existingDestination),
            $this->computeText($tpl->getContent(), $user, $existingQuote, $existingSite, $existingDestination)
        );
    }

    /**
     * This method will iterate over each placeholder instance, get its placeholder and the replacement text and replace the former with the latter.
     * To add new placeholder replacement you must :
     * - create a new class which implements PlaceholderReplacer
     * - implement methods to define your placeholder and replacement text
     * - instantiate the class in PlaceholderFactory::getPlaceholderReplacerInstances
     *
     * @param  string           $text
     * @param  User             $user
     * @param  Quote|null       $quote
     * @param  Site|null        $site
     * @param  Destination|null $destination
     * @return string
     */
    private function computeText(string $text, User $user, ?Quote $quote, ?Site $site, ?Destination $destination) : string
    {
        foreach ($this->placeholderFactory->getPlaceholderReplacerInstances() as $placeholderReplacer) {
            $placeholder = $placeholderReplacer->getPlaceholder();
            $replacementText = $placeholderReplacer->getReplacementText($user, $quote, $destination, $site);

            $text = str_replace($placeholder, $replacementText, $text);
        }

        return $text;
    }
}
