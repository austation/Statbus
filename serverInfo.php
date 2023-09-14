<?php

require_once __DIR__.'/vendor/autoload.php';
$settings = require_once(__DIR__.'/app/settings.php');
$settings = $settings['alt_db'];

define('SERVERINFO', 'https://tgstation13.org/serverinfo.json');

$client = new GuzzleHttp\Client();
$res = $client->request('GET', SERVERINFO);
$data = json_decode($res->getBody());
unset($data->refreshtime);
$insert = [];
foreach($data as $d) {
    if(!empty($d->error)) {
        continue;
    }
    $insert[] = [$d->serverdata->dbname,$d->serverdata->address,$d->serverdata->port,$d->players, $d->admins, $d->round_id];
}

$host = $settings['host'];
$db   = $settings['database'];
$user = $settings['username'];
$pass = $settings['password'];
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $options);

$stmt = $pdo->prepare("INSERT INTO population (`server`, `address`, `port`, `players`, `admins`, `round_id`) VALUES (?,?,?,?,?,?)");
try {
    $pdo->beginTransaction();
    foreach ($insert as $row) {
        $stmt->execute($row);
    }
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollback();
    throw $e;
}
