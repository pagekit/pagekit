@extends('view://system/widgets/menu/style.base.razr.php')

@block('itemAttributes') class="@block('itemClasses')@((active ? 'uk-active')~(header ? ' uk-nav-header')~(divider ? ' uk-nav-divider')|trim)@endblock"@endblock

@block('children')
<ul>
    @include('view://system/widgets/menu/style.subnav.razr.php', ['root' => item])
</ul>
@endblock