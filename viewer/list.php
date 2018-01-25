<?php
    require_once '../src/credentials.php';
    header('Content-Type: text/html; charset=UTF-8');
    $connection = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    $photomicrographs = $connection->query('select * from photomicrograph');
    $planeCount = $connection->prepare('select count(*) from focal_plane_image where photomicrograph_id = ?');
?>
<!DOCTYPE html>
<html>
    <head lang="en">
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <main>
            <ul>
                <?php foreach ($photomicrographs as $record): ?>
                    <li>
                        <a href="/viewer/?id=<?php echo $record->id ?>"><?php echo $record->title ?></a>
                        (<?php $planeCount->execute([$record->id]); echo $planeCount->fetchColumn() ?>)
                    </li>
                <?php endforeach ?>
            </ul>
        </main>
    </body>
</html>
