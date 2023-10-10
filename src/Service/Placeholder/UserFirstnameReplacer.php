<?php

class UserFirstnameReplacer implements PlaceholderReplacer
{

    /**
     * {@inheritdoc}
     */
    public function getPlaceholder(): string
    {
        return '[user:first_name]';
    }

    /**
     * {@inheritdoc}
     */
    public function getReplacementText(User $user, ?Quote $quote, ?Destination $destination, ?Site $site): string
    {
        return ucfirst(mb_strtolower($user->getFirstname()));
    }
}