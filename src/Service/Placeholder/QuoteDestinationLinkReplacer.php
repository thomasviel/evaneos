<?php

class QuoteDestinationLinkReplacer implements PlaceholderReplacer
{

    /**
     * {@inheritdoc}
     */
    public function getPlaceholder(): string
    {
        return '[quote:destination_link]';
    }

    /**
     * {@inheritdoc}
     */
    public function getReplacementText(User $user, ?Quote $quote, ?Destination $destination, ?Site $site): string
    {
        return $quote && $destination && $site ? $site->getUrl() . '/' . $destination->getCountryName() . '/quote/' . $quote->getId() : $this->getPlaceholder();
    }
}