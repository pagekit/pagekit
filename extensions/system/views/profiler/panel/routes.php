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

    <?php foreach($collector->getRoutes() as $name => $route) : ?>

        <tr<?php echo ($name == $current ? ' style="background-color:#e5e5e5;font-weight:bold";' : '') ?>>
            <td><?php echo $name ?></td>
            <td><?php echo $route['pattern'] ?></td>
            <td><?php echo $abbrClass($route['controller']) ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>