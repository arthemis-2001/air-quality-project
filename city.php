<?php 

require __DIR__ . '/inc/functions.inc.php'; 
$city = null;
if (!empty($_GET['city'])) {
    $city = $_GET['city'];
}

$filename = null;
if (!empty($city)) {
    $contents = file_get_contents(__DIR__ . '/data/index.json');
    $cities = json_decode($contents, true);
    foreach ($cities as $currentCity) {
        if ($currentCity['city'] === $city) {
            $filename = $currentCity['filename'];
            break;
        }
    }
}

if (!empty($filename)) {
    $results = json_decode(
        file_get_contents('compress.bzip2://' . __DIR__ . '/data/' . $filename),
        true
    )['results'];

    $stats = [];
    foreach ($results as $result) {
        if ($result['parameter'] !== 'pm25') continue;

        $month = substr($result['date']['local'], 0, 7);
        if (!isset($stats[$month])) {
            $stats[$month] = [];
        }
        $stats[$month][] = $result['value'];
    }
}
?>

<?php require __DIR__ . '/views/header.inc.php'; ?>
<?php if (empty($city)): ?>
    <p>City could not be loaded.</p>
<?php else: ?>
    <?php if (!empty($stats)): ?>
        <table>
            <?php foreach ($stats as $month => $measurements): ?>
                <tr>
                    <th><?= escape($month) ?></th>
                    <td><?= escape(array_sum($measurements) / count($measurements)) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/views/footer.inc.php'; ?>