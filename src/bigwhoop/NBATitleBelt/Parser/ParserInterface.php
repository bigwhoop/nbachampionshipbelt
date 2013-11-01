<?php
namespace bigwhoop\NBATitleBelt\Parser;

use bigwhoop\NBATitleBelt\Game;

interface ParserInterface
{
    /**
     * @return Game[]
     */
    public function getGames();
}
 