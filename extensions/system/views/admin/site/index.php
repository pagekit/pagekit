<?php

$this['scripts']->queue('site-application', 'extensions/system/app/site/application.js', ['application-directives', 'angular-route', 'angular-resource', 'uikit-nestable']);
$this['scripts']->queue('site-controllers', 'extensions/system/app/site/controllers.js', 'site-application');

?>

<div data-app="site" ng-cloak ng-view></div>
