<?php

class Template
{
    private int $id;
    private string $subject;
    private string $content;

    public function __construct(int $id, string $subject, string $content)
    {
        $this->id = $id;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param  string $subject
     * @return Template
     */
    public function setSubject(string $subject): Template
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param  string $content
     * @return Template
     */
    public function setContent(string $content): Template
    {
        $this->content = $content;
        return $this;
    }


}
