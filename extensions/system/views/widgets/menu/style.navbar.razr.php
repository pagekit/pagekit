@extends('view://system/widgets/menu/style.nav.razr.php')

@block('menuAttributes')class="uk-navbar-nav"@endblock

@block('itemAttributes')@parent()@(hasChildren && root.depth == 0 ? ' data-uk-dropdown')@endblock

@block('children')
<div class="uk-dropdown uk-dropdown-navbar">
    <ul class="uk-nav uk-nav-navbar">
        @include('view://system/widgets/menu/style.subnav.razr.php', ['root' => item])
    </ul>
</div>
@endblock