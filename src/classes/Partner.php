<?php

/**
 *
 */

class Partner extends User
{
    public $quotas = [];

    public function getQuotas()
    {
        return $this->quotas;
    }

    public function recieveQuota(Association $association, Quota $quota)
    {
        $this->quotas[$association->nickname] = $quota->setPartner($this);
    }

    public function payQuota(Quota $quota, float $money)
    {
        $quota->receiveMoney($this, $money);
    }

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

    public function enterEvent(Events $event) {
        $event->createRegistration($this);
    }
}

