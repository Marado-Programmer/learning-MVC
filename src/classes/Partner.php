<?php

/**
 *
 */

class Partner extends User
{
    private $dues = [];

    public function listYourAssociations()
    {
        $list = "{$this->username}'s associations where he's president list:\n";
        foreach ($this->associations as $association)
            $list .= $association;
        return $list;
    }

    public function exitYourAssociation(int $i, User &$user = null)
    {
        if (in_array($this->associations[$i], $this->yourAssociations)) {
            if (!isset($user)) {
                echo "error: you are the president of this association, please give a user to pass the president role.";

                return;
            }
            $this->associations[$i]->president = $user;
        }
        unset($this->associations[$i]);
    }

    public function recieveDue(Association $association, float $price, DateTime $endDate, DateTime $startDate = null)
    {
        $this->dues[] = new Dues($this, $association, $price, $endDate, $startDate);
    }

    public function enterEvent(Events $event) {
        $event->createRegistration($this);
    }

    public function createNews(
        Association $association,
        string $title,
        array $image,
        string $article
    ) {
        $association->publishNews(clone $this, $title, $image, $article);
    }
}

