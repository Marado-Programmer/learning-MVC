<?php

/**
 *
 */

class Events
{
    public $id, $title, $description;

    public $associations;

    public $registrations;

    public function __construct(Association $association, string $title, string $description)
    {
        $this->title = $title;
        $this->description = $description;
        $this->associations = ['ini' => $association];
    }

    public function addAssociation(Association $association)
    {
        if ($this->associations['ini'] !== $association && in_array($association, $this->associations))
            $this->associations[] = $association;
    }

    public function removeAssociation(Association $association)
    {
        if ($this->associations['ini'] !== $association && in_array($association, $this->associations)) {
            foreach ($this->associations as $i => $v)
                if ($v === $association) {
                    unset($this->associations[$i]);
                    break;
                }
            $this->associations = array_values($this->associations);
        }
    }

    public function __toString()
    {
        return "Event titled {$this->title} by {$this->association['ini']->name}: {$this->description}.\n"
            . "\tNumber of participating associations: " . count($this->associations);
    }
}