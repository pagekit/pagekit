<!DOCTYPE html>
<html class="uk-height-1-1">
    <head>
        <meta charset="utf-8">
        <title>Pagekit Installer Errors</title>
        <link href="@url('extension://system/theme/favicon.ico')" rel="shortcut icon" type="image/x-icon">
        <link href="vendor/assets/uikit/css/uikit.pagekit-installer.min.css" rel="stylesheet">
        <link href="extensions/installer/theme/css/installer.css" rel="stylesheet">
        <script src="vendor/assets/jquery/jquery.js"></script>
        <script src="vendor/assets/uikit/js/uikit.min.js"></script>
    </head>
    <body class="uk-height-1-1">

        <div class="tm-slide uk-vertical-align uk-text-center">

            <div class="tm-container uk-panel uk-panel-box uk-vertical-align-middle">

                <h1 class="uk-text-center">System Requirements</h1>

                <p class="uk-text-center">Your server doesn't meet the minimum system requirements. Please fix the following issues to proceed.</p>

                <?php foreach ($failed as $req) : ?>
                <p>
                    <span class="uk-badge uk-badge-danger">Error</span> <strong><?php echo $req->getTestMessage() ?></strong><br>
                    <span class="uk-badge">Fix</span> <i><?php echo $req->getHelpHtml() ?></i>
                </p>
                <?php endforeach ?>

            </div>

        </div>

    </body>
</html>