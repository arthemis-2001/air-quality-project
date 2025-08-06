<?php 

require __DIR__ . '/inc/functions.inc.php'; 
$city = null;
if (!empty($_GET['city'])) {
    $city = $_GET['city'];
}

$filename = null;
$cityInformation = [];
if (!empty($city)) {
    $contents = file_get_contents(__DIR__ . '/data/index.json');
    $cities = json_decode($contents, true);
    foreach ($cities as $currentCity) {
        if ($currentCity['city'] === $city) {
            $filename = $currentCity['filename'];
            $cityInformation = $currentCity;
            break;
        }
    }
}

if (!empty($filename)) {
    $results = json_decode(
        file_get_contents('compress.bzip2://' . __DIR__ . '/data/' . $filename),
        true
    )['results'];

    $units = [
        'pm25' => null,
        'pm10' => null
    ];

    foreach ($results as $result) {
        if (!empty($units['pm25']) && !empty($units['pm10'])) break;
        if ($result['parameter'] === 'pm25') {
            $units['pm25'] = $result['unit'];
        }
        if ($result['parameter'] === 'pm10') {
            $units['pm10'] = $result['unit'];
        }
    }

    $stats = [];
    foreach ($results as $result) {
        if ($result['parameter'] !== 'pm25' && $result['parameter'] !== 'pm10') continue;
        if ($result['value'] < 0) continue;

        $month = substr($result['date']['local'], 0, 7);
        if (!isset($stats[$month])) {
            $stats[$month] = [
                'pm25' => [],
                'pm10' => []
            ];
        }
        
        $stats[$month][$result['parameter']][] = $result['value'];
    }
    // var_dump($stats);
}
?>

<?php require __DIR__ . '/views/header.inc.php'; ?>
<?php if (empty($city)): ?>
    <p>City could not be loaded.</p>
<?php else: ?>
    <?php if (!empty($stats)): ?>
        <h1><?= escape($cityInformation['city']) ?> <?= escape($cityInformation['flag']) ?></h1>
        <h2><?= escape($cityInformation['country']) ?></h2>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>PM2.5 concentration</th>
                    <th>PM10 concentration</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats as $month => $measurements): ?>
                    <tr>
                        <th><?= escape($month) ?></th>
                        <td>
                            <?= escape(round(array_sum($measurements['pm25']) / count($measurements['pm25']), 2)) ?>
                            <?= escape($units['pm25']) ?>
                        </td>
                        <td>
                            <?= escape(round(array_sum($measurements['pm10']) / count($measurements['pm10']), 2)) ?>
                            <?= escape($units['pm10']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/views/footer.inc.php'; ?>
