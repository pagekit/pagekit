<a title="Database" class="pf-parent">
	<div class="pf-icon pf-icon-database"></div> <?php echo $collector->getQueryCount() ?>
</a>

<div class="pf-dropdown">

    <table class="pf-table pf-table-dropdown">
        <tbody>
            <tr>
                <td>Queries</td>
                <td><?php echo $collector->getQueryCount() ?></td>
            </tr>
            <tr>
                <td>Time</td>
                <td><?php echo round($collector->getTime() * 1000) ?> ms</td>
            </tr>
            <tr>
                <td>Driver</td>
                <td><?php echo $collector->getDriver() ?></td>
            </tr>
        </tbody>
    </table>

</div>