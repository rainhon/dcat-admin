@php
    $depth = $item['depth'] ?? 0;

    $horizontal = config('admin.layout.horizontal_menu');

    $defaultIcon = config('admin.menu.default_icon', 'feather icon-circle');
@endphp

@if($builder->visible($item))
    <li class="nav-item">
        <a data-id="{{ $item['id'] ?? '' }}" @if(mb_strpos($item['uri'], '://') !== false) target="_blank" @endif
            href="{{ $builder->getUrl($item['uri']) }}"
            class="nav-link {!! $builder->isActive($item) ? 'active' : '' !!}">
            {!! str_repeat('&nbsp;', $depth) !!}
            <p>
                {!! $builder->translate($item['title']) !!}
            </p>
        </a>
    </li>
@endif
