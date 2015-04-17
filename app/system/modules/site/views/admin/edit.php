<ul class="uk-tab" v-el="tab">
    <?php foreach ($sections as $name => $section) : ?>
    <li><a><?= __($name) ?></a></li>
    <?php endforeach ?>
</ul>

<div class="uk-switcher uk-margin" v-el="content">
    <?php foreach ($sections as $subsections) : ?>
    <div>
        <?php
            foreach ($subsections as $section) {
                $params = array_merge($section, ['node' => $node]);
                if (is_callable($section['view'])) {
                    echo call_user_func($section['view'], $node);
                } else {
                    echo $view->render($section['view'], ['node' => $node]);
                }
            }
        ?>
    </div>
    <?php endforeach ?>
</div>
