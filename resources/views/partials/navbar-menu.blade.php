@php
    $depth = $item['depth'] ?? 0;

    $horizontal = config('admin.layout.horizontal_menu');

    $defaultIcon = config('admin.menu.default_icon', 'feather icon-circle');

    $item['isActive'] = $builder->isActive($item);
@endphp

@if($builder->visible($item))
    <li class="nav-item flex-grow-1">
        <a  class="nav-link {!! $item['isActive'] ? 'active' : '' !!}"
            data-id="{{ $item['id'] ?? '' }}" >
            {!! str_repeat('&nbsp;', $depth) !!}
            <p>
                {!! $builder->translate($item['title']) !!}
            </p>
        </a>
    </li>
@endif
