<?php
require __DIR__ . '/../vendor/autoload.php';

if (!isset($argv[1])) {
    exit("First parameter must be the year you want to scrape.\n");
}

$year = (int)$argv[1];

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client();
$crawler = $client->request('GET', 'http://www.basketball-reference.com/leagues/NBA_' . ($year + 1) . '_games.html');

$csv = fopen(__DIR__ . '/../data/' . $year . '.csv', 'w');

$crawler->filter('#div_games tbody tr')->each(function(Crawler $tr, $i) use ($csv) {
    $fields = [];
    $tr->filter('td')->each(function(Crawler $td, $i) use (&$fields) {
        /** @var Crawler $td */
        $fields[] = $td->text();
    });
    
    fputcsv($csv, $fields, ',', '"');
});

fclose($csv);

echo "DONE.\n";
exit(0);