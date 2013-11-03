<?php
namespace App;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

require __DIR__ . '/../vendor/autoload.php';

if (!isset($argv[1])) {
    exit("First parameter must be the year of the seasons you want to scrape.\n");
}

$season = $argv[1];

if ($season == 'all') {
    $seasons = range(1950, 2013);
} else {
    $season = (int)$season;
    if ($season < 1950 || $season > 2013) {
        exit("The season must be between 1950 and 2013.\n");
    }
    $seasons = [$season];
}
    
$csv = fopen(__DIR__ . '/../data/' . $season . '.csv', 'w');

$client = new Client();

foreach ($seasons as $year) {
    printf("Processing season %d ... ", $year);
    
    $crawler = $client->request('GET', 'http://www.basketball-reference.com/leagues/NBA_' . ($year + 1) . '_games.html');
    
    $crawler->filter('#div_games tbody tr')->each(function(Crawler $tr) use ($csv) {
        $fields = [];
        $tr->filter('td')->each(function(Crawler $td) use (&$fields) {
            $fields[] = $td->text();
        });
        fputcsv($csv, $fields, ',', '"');
    });
    
    print("DONE.\n");
}

fclose($csv);

exit(0);