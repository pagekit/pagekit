<?php

$this['scripts']->queue('site-application', 'extensions/system/app/site.js', ['application', 'angular-route', 'angular-resource', 'uikit-nestable']);
$this['scripts']->queue('site-directives', 'extensions/system/app/directives.js', 'site-application');
$this['scripts']->queue('site-controllers', 'extensions/system/app/controllers.js', 'site-application');

?>

<div data-app="site" ng-cloak ng-view></div>
