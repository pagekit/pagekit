<?php

$this['scripts']->queue('site-application', 'extensions/system/modules/site/app/application.js', ['application-directives', 'angular-route', 'angular-resource', 'uikit-nestable']);
$this['scripts']->queue('site-controllers', 'extensions/system/modules/site/app/controllers.js', 'site-application');

?>

<div data-app="site" ng-cloak ng-view></div>
