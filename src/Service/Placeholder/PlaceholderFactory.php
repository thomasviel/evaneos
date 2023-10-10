<?php

class PlaceholderFactory
{
    /**
     * Get all instances of PlaceholderReplacer.
     *
     * @return PlaceholderReplacer[]
     */
    public function getPlaceholderReplacerInstances() : array
    {
        return [
            new QuoteDestinationNameReplacer(),
            new QuoteDestinationLinkReplacer(),
            new QuoteSummaryHtmlReplacer(),
            new QuoteSummaryReplacer(),
            new UserFirstnameReplacer()
        ];
    }
}