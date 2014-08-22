<?php
namespace bigwhoop\NBATitleBelt\Parser;

use bigwhoop\NBATitleBelt\Game;
use bigwhoop\NBATitleBelt\Team;

class BBReferenceParser implements ParserInterface
{
    const MODE_TEAMS      = 1; // New Jersey Nets and Brooklyn Nets are not the same
    const MODE_FRANCHISES = 2; // New Jersey Nets are represented as Brooklyn Nets
    
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
        'Charlotte Hornets' => 'CHA',
        'Minnesota Timberwolves' => 'MIN',
        'New York Knicks' => 'NYK',
        'Orlando Magic' => 'ORL',
        'Brooklyn Nets' => 'BKN',
        'New Jersey Nets' => 'NJN',
        'New Orleans Pelicans' => 'NOP',
        'New Orleans/Oklahoma City Hornets' => 'NOK',
        'Seattle SuperSonics' => 'SEA',
        'Vancouver Grizzlies' => 'VAN',
        'Charlotte Bobcats'   => 'CHA',
    ];
    
    /** @var array */
    private static $franchises = [
        'ATL' => ['Atlanta Hawks', 'St. Louis Hawks', 'Milwaukee Hawks', 'Tri-Cities Blackhawks'],
        'BOS' => ['Boston Celtics'],
        'BKN' => ['Brooklyn Nets', 'New Jersey Nets', 'New York Nets'],
        'CHA' => ['Charlotte Hornets', 'Charlotte Bobcats'],
        'CHI' => ['Chicago Bulls'],
        'CLE' => ['Cleveland Cavaliers'],
        'DAL' => ['Dallas Mavericks'],
        'DEN' => ['Denver Nuggets'],
        'DET' => ['Detroit Pistons', 'Fort Wayne Pistons'],
        'GSW' => ['Golden State Warriors', 'San Francisco Warriors', 'Philadelphia Warriors'],
        'HOU' => ['Houston Rockets', 'San Diego Rockets'],
        'IND' => ['Indiana Pacers'],
        'LAC' => ['Los Angeles Clippers', 'San Diego Clippers', 'Buffalo Braves'],
        'LAL' => ['Los Angeles Lakers', 'Minneapolis Lakers'],
        'MEM' => ['Memphis Grizzlies', 'Vancouver Grizzlies'],
        'MIA' => ['Miami Heat'],
        'MIL' => ['Milwaukee Bucks'],
        'MIN' => ['Minnesota Timberwolves'],
        'NOP' => ['New Orleans Pelicans', 'New Orleans Hornets', 'New Orleans/Oklahoma City Hornets'],
        'NYK' => ['New York Knicks'],
        'OKC' => ['Oklahoma City Thunder', 'Seattle SuperSonics'],
        'ORL' => ['Orlando Magic'],
        'PHI' => ['Philadelphia 76ers', 'Syracuse Nationals'],
        'PHX' => ['Phoenix Suns'],
        'POR' => ['Portland Trail Blazers'],
        'SAC' => ['Sacramento Kings', 'Kansas City Kings', 'Kansas City-Omaha Kings', 'Cincinnati Royals', 'Rochester Royals'],
        'SAS' => ['San Antonio Spurs'],
        'TOR' => ['Toronto Raptors'],
        'UTA' => ['Utah Jazz', 'New Orleans Jazz'],
        'WAS' => ['Washington Wizards', 'Washington Bullets', 'Capital Bullets', 'Baltimore Bullets', 'Chicago Zephyrs', 'Chicago Packers'],
        // ---
        'AND' => ['Anderson Packers'],
        'BLB' => ['Baltimore Bullets'],
        'CHS' => ['Chicago Stags'],
        'DNN' => ['Denver Nuggets'],
        'INO' => ['Indianapolis Olympians'],
        'SHE' => ['Sheboygan Red Skins'],
        'STB' => ['St. Louis Bombers'],
        'WSH' => ['Washington Capitols'],
        'WAT' => ['Waterloo Hawks'],
    ];
    
    
    /** @var string */
    private $csvPath = '';

    /** @var int */
    private $mode = self::MODE_TEAMS;


    /**
     * @return Team[]
     */
    static public function getFranchises()
    {
        $teams = [];
        foreach (self::$franchises as $id => $names) {
            $teams[] = new Team($id, $names);
        }
        return $teams;
    }
    
    
    /**
     * @param string $csvPath
     */
    public function __construct($csvPath)
    {
        $this->csvPath = $csvPath;
    }


    /**
     * @param int $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
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
     * @param string $teamName
     * @return Team
     */
    private function getTeam($teamName)
    {
        $id = $this->getTeamId($teamName);
        return new Team($id, self::$franchises[$id]);
    }


    /**
     * @param string $teamName
     * @return string
     * @throws \LogicException
     * @throws \RuntimeException
     */
    private function getTeamId($teamName)
    {
        switch ($this->mode)
        {
            case self::MODE_TEAMS:
                if (!array_key_exists($teamName, self::$teams)) {
                    throw new \RuntimeException("Team ID for '$teamName' is not defined.");
                }
                return self::$teams[$teamName];
            
            case self::MODE_FRANCHISES:
                foreach (self::$franchises as $key => $teamNames) {
                    if (in_array($teamName, $teamNames)) {
                        return $key;
                    }
                }
                throw new \RuntimeException("Team ID for '$teamName' is not defined.");
            
            default: throw new \LogicException("Unexpected mode: {$this->mode}.");
        }
    }


    /**
     * @return Game[]
     */
    public function getGames()
    {
        $data = $this->loadData();
        
        $games = [];
        foreach ($data as $row) {
            list($dateStr,,$awayTeamStr,$awayTeamScore,$homeTeamStr,$homeTeamScore) = $row;
            $games[] = new Game(
                \DateTime::createFromFormat('D, M j, Y H:i:s', $dateStr . ' 00:00:00'),
                $this->getTeam($homeTeamStr),
                (int)$homeTeamScore,
                $this->getTeam($awayTeamStr),
                (int)$awayTeamScore
            );
        }
        
        return $games;
    }
}
 
