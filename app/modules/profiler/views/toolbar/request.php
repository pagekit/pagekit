<?php

$request = $collector->getController();

if ($request == 'n/a') {
    $request = [];
}

$request['route'] = $collector->getRoute();
$request['status'] = $collector->getStatusCode();

$parent = false;
if (isset($request['class']) && $request['class']) {
	$request['link'] = getFileLink($request['file'], $request['line']);
}

?>

<a<?php echo $parent ? ' class="pf-parent"' : '' ?> title="Request">
    <div class="pf-icon pf-icon-request"></div>
	<span class="pf-badge"><?php echo $request['status'] ?></span>
	<?php echo $request['route'] ? $request['route'] : '-' ?>
</a>

<?php if ($parent) : ?>
<div class="pf-dropdown">

    <table class="pf-table pf-table-dropdown">
        <tbody>
            <tr>
                <td>Class</td>
                <?php if ($request['link']) : ?>
                <td><a href="<?php echo $request['link'] ?>"><?php echo $request['class'] ?></a></td>
				<?php else: ?>
                <td><?php echo $request['class'] ?></td>
				<?php endif ?>
            </tr>
            <tr>
                <td>Method</td>
                <td><?php echo $request['method'] ?></td>
            </tr>
        </tbody>
    </table>

</div>
<?php endif ?>