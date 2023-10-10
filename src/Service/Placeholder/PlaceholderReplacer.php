<?php

/**
 * Interface to implement in order to add new placeholder replacement text.
 */
interface PlaceholderReplacer
{
    /**
     * Get text to replace from template.
     *
     * @return string
     */
    public function getPlaceholder() : string;

    /**
     * Get replacement text.
     *
     * @param  User             $user
     * @param  Quote|null       $quote
     * @param  Destination|null $destination
     * @param  Site|null        $site
     * @return string
     */
    public function getReplacementText(User $user, ?Quote $quote, ?Destination $destination, ?Site $site) : string;
}