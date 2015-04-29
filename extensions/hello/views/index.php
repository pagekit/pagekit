<?php $view->style('hello', 'extensions/hello/assets/css/hello.css') ?>

<div class="hello">
    <h1>Hello <?= $names[0] ?></h1>

    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloribus modi optio, maiores eum velit debitis, id voluptatum aspernatur enim vitae deserunt, veniam quidem nesciunt amet architecto reiciendis vero, laborum omnis.</p>
</div>


<p><?= _c("{0}: No names|one: One name|more: %names% names", count($names), ["%names%" => count($names)]) ?><p>


<?php foreach ($names as $name): ?>
    <p><?= __("Hello %name%!", ["%name%" => $name]) ?></p>
<?php endforeach ?>
