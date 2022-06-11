@php
    $depth = $item['depth'] ?? 0;

    $horizontal = config('admin.layout.horizontal_menu');

    $defaultIcon = config('admin.menu.default_icon', 'feather icon-circle');

    $item['isActive'] = $builder->isActive($item);
@endphp

@if($builder->visible($item))
    <li class="nav-item flex-grow-1">
        <a @if(mb_strpos($item['uri'], '://') !== false) target="_blank" @endif
            href="{{ $builder->getUrl($item['uri']) }}"
            class="nav-link {!! $item['isActive'] ? 'active' : '' !!}"
            data-id="{{ $item['id'] ?? '' }}" 
            data-no-pjax="{{ $item['no_pjax'] ?? 0 }}">
            {!! str_repeat('&nbsp;', $depth) !!}
            <p>
                {!! $builder->translate($item['title']) !!}
            </p>
        </a>
    </li>
@endif
