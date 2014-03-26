@extends('view://system/widgets/menu/style.base.razr.php')

@block('menuAttributes')class="uk-nav uk-nav-offcanvas" data-uk-nav@endblock

@block('recursion')@include('theme://alpha/views/renderer/menu.offcanvas.razr.php', ['root' => item])@endblock