<?php $info = $collector->getInfo() ?>
<h1>Information</h1>

<h2>System</h2>
<table class="pf-table pf-table-dropdown">
    <tbody>
        <tr>
            <td>Pagekit</td>
            <td><?php echo $info["version"] ?></td>
        </tr>
        <tr>
            <td>Server</td>
            <td><?php echo $info["server"] ?></td>
        </tr>
        <tr>
            <td>Useragent</td>
            <td><?php echo $info["useragent"] ?></td>
        </tr>
    </tbody>
</table>

<h2>PHP</h2>
<table class="pf-table pf-table-dropdown">
    <tbody>
        <tr>
            <td>PHP</td>
            <td><?php echo $info['phpversion'] ?></td>
        </tr>
        <tr>
            <td>PHP SAPI</td>
            <td><?php echo $info['sapi_name'] ?></td>
        </tr>
        <tr>
            <td>System</td>
            <td><?php echo $info['php'] ?></td>
        </tr>
        <tr>
            <td>Extensions</td>
            <td><?php echo $info['extensions'] ?></td>
        </tr>
    </tbody>
</table>

<h2>Database</h2>
<table class="pf-table pf-table-dropdown">
    <tbody>
        <tr>
            <td>Driver</td>
            <td><?php echo $info["dbdriver"] ?></td>
        </tr>
        <tr>
            <td>Version</td>
            <td><?php echo $info["dbversion"] ?></td>
        </tr>
        <tr>
            <td>Client</td>
            <td><?php echo $info["dbclient"] ?></td>
        </tr>
    </tbody>
</table>