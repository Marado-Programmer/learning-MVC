<?php

/**
 * 
 */

class News
{
    public $id, $title, $article, $image;
    public $publishTime;
    public $association;

    public function __construct(
        Association $association,
        Partner $author,
        string $title,
        string $image,
        string $article,
        DateTime $publishTime,
        ?DateTime $lastEditTime = null,
        ?int $id = null
    ) {
        $this->association = $association;        
        $this->author = $author;
        $this->title = $title;
        $this->image = $image;
        $this->article = $article;
        $this->publishTime = $publishTime;
        $this->lastEditTime = $lastEditTime ?? $publishTime;
        $this->id = $id ?? -1;
    }

    public function readNewsSimple()
    {
        return "(#{$this->id})News --- {$this->title}: {$this->article}\n";
    }

    public function __toString()
    {
        return "<p><a href=\"" . HOME_URI . '/article/' . $this->id . '">' . "(#{$this->id})News --- {$this->title}:</a></p><ul>\n"
            . "\t<li>publish time -> " . $this->publishTime->format('Y-m-d H:i:s') . "</li>\n"
            . "\t<li>association -> <a href=\"" . HOME_URI . '/@' . $this->association->nickname . '">' . $this->association->name . "</a></li></ul>\n\n";
    }
}
