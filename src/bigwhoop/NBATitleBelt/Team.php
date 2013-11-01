<?php
namespace bigwhoop\NBATitleBelt;

class Team
{
    /** @var string  */
    private $name = '';


    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param Team $team
     * @return bool
     */
    public function isSame(Team $team)
    {
        return $this->getName() == $team->getName();
    }
}
 