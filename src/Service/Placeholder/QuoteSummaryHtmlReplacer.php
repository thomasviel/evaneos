<?php

class QuoteSummaryHtmlReplacer implements PlaceholderReplacer
{

    /**
     * {@inheritdoc}
     */
    public function getPlaceholder(): string
    {
        return '[quote:summary_html]';
    }

    /**
     * {@inheritdoc}
     */
    public function getReplacementText(User $user, ?Quote $quote, ?Destination $destination, ?Site $site): string
    {
        return $quote ? Quote::renderHtml($quote) : $this->getPlaceholder();
    }
}