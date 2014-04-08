@extends('view://system/widgets/menu/style.nav.razr.php')

@block('menuAttributes') class="uk-navbar-nav@(options.classes ? ' '~options.classes : '')"@endblock

@block('itemAttributes')@parent()@(item.hasChildren ? ' data-uk-dropdown')@endblock

@block('children')
<div class="uk-dropdown uk-dropdown-navbar">
    <ul class="uk-nav uk-nav-navbar">
        @include('view://system/widgets/menu/style.subnav.razr.php', ['root' => item])
    </ul>
</div>
@endblock