<!DOCTYPE html>
<html>
    <head>
        <title>Pagekit Installer Errors</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <link href="app/system/modules/theme/favicon.ico" rel="shortcut icon" type="image/x-icon">
        <link href="app/system/modules/theme/apple_touch_icon.png" rel="apple-touch-icon-precomposed">
        <link href="app/installer/assets/css/installer.css" rel="stylesheet">
    </head>
    <body>

        <div class="uk-height-viewport uk-flex uk-flex-center uk-flex-middle uk-text-center">
            <div class="tm-container">

                <img class="uk-margin-large-bottom" src="app/system/assets/images/pagekit-logo-large-black.svg" alt="Pagekit">

                <div class="uk-panel uk-panel-box">
                    <h1>System Requirements</h1>
                    <p>Please fix the following issues to proceed.</p>
                    <?php foreach ($failed as $req) : ?>
                    <p>
                        <span class="uk-badge uk-badge-danger">Error</span> <?php echo $req->getTestMessage() ?>
                    </p>
                    <p>
                        <span class="uk-badge">Fix</span> <?php echo $req->getHelpHtml() ?>
                    </p>
                    <?php endforeach ?>
                </div>

            </div>
        </div>

    </body>
</html>