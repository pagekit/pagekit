<?php

    function getFileLink($file, $line)
    {
        $fileLinkFormat = ini_get('xdebug.file_link_format');
        if ($fileLinkFormat && file_exists($file)) {
            return strtr($fileLinkFormat, ['%f' => $file, '%l' => $line]);
        }

        return false;
    }

?>

<div id="pk-profiler" class="pf-profiler">

    <div class="pf-navbar">

        <ul class="pf-navbar-nav">

        <?php foreach ($profiler->all() as $name => $collector) : ?>
            <?php if ($toolbarview = $profiler->getToolbarView($name) and $profile->hasCollector($name)) : ?>
                <li data-name="<?php echo $name ?>">
                    <?php $collector = $profile->getcollector($name); ?>
                    <?php include($toolbarview); ?>
                </li>
            <?php endif ?>
        <?php endforeach ?>
        </ul>

        <a class="pf-close"></a>

    </div>

    <?php foreach ($profiler->all() as $name => $collector) : ?>
        <?php if ($panelview = $profiler->getPanelView($name) and $profile->hasCollector($name)) : ?>
            <div class="pf-profiler-panel" data-panel="<?php echo $name ?>">
                <?php $collector = $profile->getcollector($name); ?>
                <?php include($panelview); ?>
            </div>
        <?php endif ?>
    <?php endforeach ?>

</div>