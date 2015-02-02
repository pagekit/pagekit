<?php
$abbrClass = function($class)
{
    $parts = explode('\\', $class);
    $short = array_pop($parts);

    return sprintf("<abbr title=\"%s\">%s</abbr>", $class, $short);
};

$displayListener = function($listener) use ($abbrClass) {
    if ($listener['type'] == "Closure") {
        return 'Closure ('.substr($listener['file'], strlen(dirname($_SERVER['SCRIPT_FILENAME'])) + 1).' Line '. $listener['line'].')';
    } elseif ($listener['type'] == "Function") {
        return ($link = getFileLink($listener['file'], $listener['line'])) ? "<a href=\"$link\">{$listener['function']}</a>" : $listener['function'];
    } elseif ($listener['type'] == "Method") {
        return $abbrClass($listener['class']).'::'.(($link = getFileLink($listener['file'], $listener['line'])) ? "<a href=\"$link\">{$listener['method']}</a>" : $listener['method']).' ('.strtok($listener['class'], '\\').') ';
    }
}

?>
<h1>Events</h1>

<?php if (!$collector->getCalledListeners()) : ?>
    <p>
        <em>No events have been recorded. Are you sure that debugging is enabled in the kernel?</em>
    </p>
<?php else : ?>

    <h2>Called Listeners</h2>

    <table class="pf-table">
        <tbody>
            <tr>
                <th>Event name</th>
                <th>Listener</th>
                <th>Priority</th>
            </tr>
            <?php foreach($collector->getCalledListeners() as $listener) : ?>
                <tr>
                    <td><code><?php echo $listener['event'] ?></code></td>
                    <td><?php echo $displayListener($listener) ?></td>
                    <td><?php echo isset($listener['priority']) ? $listener['priority'] : '' ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <?php if ($collector->getNotCalledListeners()) : ?>

        <h2>Not Called Listeners</h2>

        <table class="pf-table">
            <tbody>
                <tr>
                    <th>Event name</th>
                    <th>Listener</th>
                    <th>Priority</th>
                </tr>
                <?php $listeners = $collector->getNotCalledListeners() ?>
                <?php $keys = array_keys($listeners) ?>
                <?php sort($keys) ?>
                <?php foreach($keys as $listener) : ?>
                    <tr>
                        <td><code><?php echo $listeners[$listener]['event'] ?></code></td>
                        <td><?php echo $displayListener($listeners[$listener]) ?></td>
                        <td><?php echo isset($listeners[$listener]['priority']) ? $listeners[$listener]['priority'] : '' ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>

    <?php endif ?>

<?php endif ?>