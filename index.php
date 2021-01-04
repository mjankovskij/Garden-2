<?php
$url = explode('/', $_SERVER['REQUEST_URI']);
$link = 'http://' . $_SERVER['SERVER_NAME'] . "$url[0]/$url[1]/$url[2]/$url[3]";

include_once __DIR__ . '/Cucumber.php';
$garden = new Cucumber;

if (isset($url[5]) && isset($url[6]) && $url[5] == 'uproot') {
    $garden->uproot($url[6]);
    if (isset($url[4])) {
        $link .= '/' . $url[4];
    }
    header("Location: $link");
}

if (isset($url[5])) {
    if ($url[5] == 'growAll') {
        $garden->growAll($_POST);
        if (isset($url[4])) {
            $link .= '/' . $url[4];
        }
        header("Location: $link");
    }
    if ($url[5] == 'growNew') {
        $garden->growNew();
        if (isset($url[4])) {
            $link .= '/' . $url[4];
        }
        header("Location: $link");
    }
    if ($url[5] == 'pickCucumbers') {
        if (isset($_POST['id']) && isset($_POST['count'])) {
            if (isset($url[4])) {
                $link .= '/' . $url[4];
            }
            if ($garden->pick($_POST['id'], $_POST['count']) == 'OK') {
                header("Location: $link");
            }else{
                header("Refresh:0; url=$link");
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agurku sodas</title>

    <link rel="stylesheet" href="<?= $link ?>/style.css">
    <link rel="icon" href="./img/logo.png">

</head>

<body>
    <header>
        <h1>Agurku sodas</h1>
        <nav>
            <a href='<?= $link ?>/garden'>Sodas</a>
            <a href="<?= $link ?>/grow">Auginimas</a>
            <a href="<?= $link ?>/pick">Skynimas</a>
        </nav>
    </header>
    <div class='container'>
        <?php
        $page = $url[4];
        if ($page != '' && file_exists(__DIR__ . '/' . $page . '.php')) {
            include_once __DIR__ . '\\' . $page . '.php';
        } else {
            include_once __DIR__ . '\garden.php';
        }
        ?>


    </div>
</body>


</html>