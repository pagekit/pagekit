@extends('view://system/widgets/menu/style.subnav.razr.php')

@block('menuAttributes') class="uk-nav uk-nav-side@(options.classes ? ' '~options.classes : '')"@endblock

@block('itemClasses')@((parent()~(item.attribute('parent') ? ' uk-parent'))|trim)@endblock

@block('children')
<ul class="uk-nav-sub">
    @include('view://system/widgets/menu/style.subnav.razr.php', ['root' => item])
</ul>
@endblock