<?php
    $abbrClass = function($class)
    {
        $parts = explode('\\', $class);
        $short = array_pop($parts);

        return sprintf("<abbr title=\"%s\">%s</abbr>", $class, $short);
    };

    $current = (string) $profile->getCollector('request')->getRoute();
?>

<h1>Routes</h1>
<table class="pf-table">
    <thead>
        <tr>
            <th>Route name</th>
            <th>Pattern</th>
            <th>Controller</th>
        </tr>
    </thead>
    <tbody>


    <?php foreach($app['router']->getRouteCollection() as $name => $route) : ?>

        <tr<?php echo ($name == $current ? ' style="background-color:#e5e5e5;font-weight:bold";' : '') ?>>
            <td><?php echo $name ?></td>
            <td><?php echo $route->getPattern() ?></td>
            <td><?php echo is_string($ctrl = $route->getDefault('_controller')) ? $abbrClass($ctrl) : 'Closure' ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>