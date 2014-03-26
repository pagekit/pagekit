@extends('view://system/widgets/menu/style.subnav.razr.php')

@block('menuAttributes')class="uk-nav uk-nav-side"@endblock

@block('itemClasses')@((parent()~(hasChildren ? ' uk-parent'))|trim)@endblock

@block('children')
<ul class="uk-nav-sub">
    @include('view://system/widgets/menu/style.subnav.razr.php', ['root' => item])
</ul>
@endblock