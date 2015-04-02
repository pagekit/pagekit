<?php if ($app['message']->peekAll()): ?>
<div class="pk-system-messages">
    <?php
        foreach ($app['message']->levels() as $level) {
            if ($messages = $app['message']->get($level)) {
                foreach ($messages as $message) {
                    printf('<div class="uk-alert uk-alert-%1$s" data-status="%1$s">%2$s</div>', $level == 'error' ? 'danger' : $level, $message);
                }
            }
        }
    ?>
</div>
<?php endif ?>