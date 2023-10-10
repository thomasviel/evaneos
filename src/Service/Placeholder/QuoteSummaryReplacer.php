<?php

class QuoteSummaryReplacer implements PlaceholderReplacer
{

    /**
     * {@inheritdoc}
     */
    public function getPlaceholder(): string
    {
        return '[quote:summary]';
    }

    /**
     * {@inheritdoc}
     */
    public function getReplacementText(User $user, ?Quote $quote, ?Destination $destination, ?Site $site): string
    {
        return $quote ? Quote::renderText($quote) : $this->getPlaceholder();
    }
}