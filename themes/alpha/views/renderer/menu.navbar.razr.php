@extends('view://system/widgets/menu/style.base.razr.php')

@block('menuAttributes')class="uk-navbar-nav"@endblock

@block('itemAttributes')@parent()@(hasChildren && root.depth == 0 ? ' data-uk-dropdown')@endblock

@block('childrenStart')
@(hasChildren && root.depth == 0 ? '<div class="uk-dropdown uk-dropdown-navbar">')<ul>
@endblock

@block('childrenEnd')
</ul>@(hasChildren && root.depth == 0 ? '</div>')
@endblock

@block('recursion')@include('theme://alpha/views/renderer/menu.navbar.razr.php', ['root' => item])@endblock