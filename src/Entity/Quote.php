<?php

class Quote
{
    private int $id;
    private int $siteId;
    private int $destinationId;
    private string $dateQuoted;

    public function __construct(int $id, int $siteId, int $destinationId, string $dateQuoted)
    {
        $this->id = $id;
        $this->siteId = $siteId;
        $this->destinationId = $destinationId;
        $this->dateQuoted = $dateQuoted;
    }


    public static function renderHtml(Quote $quote) : string
    {
        return htmlspecialchars('<p>' . $quote->getId() . '</p>');
    }

    public static function renderText(Quote $quote)  : string
    {
        return $quote->id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSiteId(): int
    {
        return $this->siteId;
    }

    /**
     * @return int
     */
    public function getDestinationId(): int
    {
        return $this->destinationId;
    }

    /**
     * @return string
     */
    public function getDateQuoted(): string
    {
        return $this->dateQuoted;
    }


}
