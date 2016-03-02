<?php
/**
 * Include the Engine classes
 */
include_once 'Engine/BasicSettings.php';
include_once 'Engine/MainController.php';
include_once 'Engine/Model.php';
include_once 'Engine/Controller.php';
include_once 'Engine/DbConn.php';
/**
 * Set up the application
 */
try {
    $settings = new BasicSettings();
    
    include $settings->controllerFileFromName($settings->getController());
    $prepare = new ReflectionClass($settings->getController());
    $controller = $prepare->newInstanceArgs(array($settings));
    $method = (in_array($settings->getMethod(), $controller->getEndpoints()) ? $settings->getMethod() : 'index');
    $controller->setView($method);
    call_user_func(array($controller, $method));
} catch (Exception $e) {
    die('Uh-oh.. something went wrong...<br />' . $e->getMessage());
}
/**
 * Render the application
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $controller->getTitle(); ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php
        foreach ($controller->getStyleSheets() as $stylesheet) {
            ?>
            <link rel="stylesheet" type="text/css" href="<?php echo $stylesheet['route']; ?>" media="<?php echo $stylesheet['media']; ?>">
            <?php
        }
        ?>
    </head>
    <body>
        <?php
        include $controller->getLayoutFile();
        ?>
    </body>
</html>
