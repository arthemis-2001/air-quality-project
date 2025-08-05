<?php require __DIR__ . '/inc/functions.inc.php'; ?>

<?php 
$contents = file_get_contents(__DIR__ . '/data/index.json');
$cities = json_decode($contents, true);
?>

<?php require __DIR__ . '/views/header.inc.php'; ?>

<ul>
    <?php foreach($cities as $city): ?>
        <li>
            <a href="city.php?<?= http_build_query(['city' => $city['city']]); ?>">
                <?= escape($city['city']) ?>,
                <?= escape($city['country']) ?>
                (<?= escape($city['flag']) ?>)
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<?php require __DIR__ . '/views/footer.inc.php'; ?>