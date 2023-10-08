<?php

class TemplateManager
{
    private QuoteRepository $quoteRepository;
    private SiteRepository $siteRepository;
    private DestinationRepository $destinationRepository;
    private ApplicationContext $applicationContext;

    public function __construct()
    {
        $this->quoteRepository = new QuoteRepository();
        $this->siteRepository = new SiteRepository();
        $this->destinationRepository = new DestinationRepository();
        $this->applicationContext = new ApplicationContext();
    }

    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        $replaced = clone($tpl);
        $replaced->setSubject($this->computeText($replaced->getSubject(), $data));
        $replaced->setContent($this->computeText($replaced->getContent(), $data));

        return $replaced;
    }

    private function computeText(string $text, array $data) : string
    {

        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote) {
            $_quoteFromRepository = $this->quoteRepository->getById($quote->getId());
            $usefulObject = $this->siteRepository->getById($quote->getSiteId());
            $destinationOfQuote = $this->destinationRepository->getById($quote->getDestinationId());

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary     = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($_quoteFromRepository),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($_quoteFromRepository),
                        $text
                    );
                }
            }

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]', $destinationOfQuote->getCountryName(), $text);
        }

        if (strpos($text, '[quote:destination_link]') !== false) {
            $destination = $this->destinationRepository->getById($quote->getDestinationId());
            $text = str_replace('[quote:destination_link]', $usefulObject->getUrl() . '/' . $destination->getCountryName() . '/quote/' . $_quoteFromRepository->getId(), $text);
        }

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof User)) ? $data['user'] : $this->applicationContext->getCurrentUser();
        if($_user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($_user->getFirstname())), $text);
        }

        return $text;
    }

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext(): ApplicationContext
    {
        return $this->applicationContext;
    }
}
