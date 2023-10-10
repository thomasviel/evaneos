<?php

class QuoteDestinationNameReplacer implements PlaceholderReplacer
{

    /**
     * {@inheritdoc}
     */
    public function getPlaceholder(): string
    {
        return '[quote:destination_name]';
    }

    /**
     * {@inheritdoc}
     */
    public function getReplacementText(User $user, ?Quote $quote, ?Destination $destination, ?Site $site): string
    {
        return $destination ? $destination->getCountryName() : $this->getPlaceholder();
    }
}