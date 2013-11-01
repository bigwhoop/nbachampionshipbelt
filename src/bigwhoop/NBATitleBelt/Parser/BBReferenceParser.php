<?php
namespace bigwhoop\NBATitleBelt\Parser;

use bigwhoop\NBATitleBelt\Game;
use bigwhoop\NBATitleBelt\Team;

class BBReferenceParser implements ParserInterface
{
    /** @var array */
    private static $teams = [
        'Washington Wizards' => 'WAS',
        'Cleveland Cavaliers' => 'CLE',
        'Dallas Mavericks' => 'DAL',
        'Los Angeles Lakers' => 'LAL',
        'Boston Celtics' => 'BOS',
        'Miami Heat' => 'MIA', 
        'Sacramento Kings' => 'SAC',
        'Chicago Bulls' => 'CHI',
        'Houston Rockets' => 'HOU',
        'Detroit Pistons' => 'DET',
        'Memphis Grizzlies' => 'MEM',
        'Los Angeles Clippers' => 'LAC',
        'San Antonio Spurs' => 'SAS',
        'New Orleans Hornets' => 'NOH',
        'Denver Nuggets' => 'DEN',
        'Philadelphia 76ers' => 'PHI',
        'Golden State Warriors' => 'GSW',
        'Phoenix Suns' => 'PHX',
        'Portland Trail Blazers' => 'POR',
        'Indiana Pacers' => 'IND',
        'Toronto Raptors' => 'TOR',
        'Utah Jazz' => 'UTA',
        'Oklahoma City Thunder' => 'OKC',
        'Atlanta Hawks' => 'ATL',
        'Milwaukee Bucks' => 'MIL',
        'Charlotte Bobcats' => 'CHA',
        'Minnesota Timberwolves' => 'MIN',
        'New York Knicks' => 'NYK',
        'Orlando Magic' => 'ORL',
        'Brooklyn Nets' => 'BKN',
        'New Jersey Nets' => 'NJN',
        'New Orleans Pelicans' => 'NOP',
        'New Orleans/Oklahoma City Hornets' => 'NOK',
        'Seattle SuperSonics' => 'SEA',
        'Charlotte Hornets' => 'CHH',
        'Vancouver Grizzlies' => 'VAN',
    ];
    
    
    /** @var string */
    private $csvPath = '';
    
    
    /**
     * @param string $csvPath
     */
    public function __construct($csvPath)
    {
        $this->csvPath = $csvPath;
    }


    /**
     * @return array
     * @throws \RuntimeException
     */
    private function loadData()
    {
        if (!file_exists($this->csvPath)) {
            throw new \RuntimeException("CSV file '{$this->csvPath}' does not exist.");
        }
        
        $fp = fopen($this->csvPath, 'r');
        
        $a = [];
        while (($row = fgetcsv($fp, null, ',', '"'))) {
            $a[] = $row;
        }
        
        return $a;
    }


    /**
     * @return Game[]
     */
    public function getGames()
    {
        $data = $this->loadData();
        
        $games = [];
        foreach ($data as $row) {
            list($dateStr,,$homeTeamStr,$homeTeamScore,$awayTeamStr,$awayTeamScore) = $row;
            $games[] = new Game(
                \DateTime::createFromFormat('D, M j, Y H:i:s', $dateStr . ' 00:00:00'),
                new Team(self::$teams[$homeTeamStr]),
                $homeTeamScore,
                new Team(self::$teams[$awayTeamStr]),
                $awayTeamScore
            );
        }
        
        return $games;
    }
}
 