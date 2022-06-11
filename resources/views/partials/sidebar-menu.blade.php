<ul class="sidebar-menu-ul @if($builder->isActive($parentItem)) active  @endif" data-id="{{ $parentItem['id'] }}" >
    @php
        $defaultIcon = config('admin.menu.default_icon', 'feather icon-circle');
    @endphp
    <li class="module-title">
        <i class="fa fa-fw {{ $parentItem['icon'] ?: $defaultIcon }}"></i>
        {!! $builder->translate($parentItem['title']) !!}
    </li>
    @foreach($items as $item)
        @php
            $depth = $item['depth'] ?? 0;

            $horizontal = config('admin.layout.horizontal_menu');

            $item['isActive'] = $builder->isActive($item);
        @endphp
        
        @if($builder->visible($item))
            @if(empty($item['children']))
                <li class="nav-item">
                    <a data-id="{{ $item['id'] ?? '' }}" @if(mb_strpos($item['uri'], '://') !== false) target="_blank" @endif
                    href="{{ $builder->getUrl($item['uri']) }}"
                    class="nav-link {!! $item['isActive'] ? 'active' : '' !!}"
                    data-no-pjax="{{ $item['no_pjax'] ?? 0 }}" >
                        {!! str_repeat('&nbsp;', $depth) !!}
                        <p>
                            {!! $builder->translate($item['title']) !!}
                        </p>
                    </a>
                </li>
            @else
                <li class="{{ $horizontal ? 'dropdown' : 'has-treeview' }} {{ $depth > 0 ? 'dropdown-submenu' : '' }} nav-item {{ $item['isActive'] ? 'menu-open' : '' }}">
                    <a href="#"  data-id="{{ $item['id'] ?? '' }}"
                    class="nav-link {{ $item['isActive'] ? ($horizontal ? 'active' : '') : '' }}
                            {{ $horizontal ? 'dropdown-toggle' : '' }}"
                            data-no-pjax="{{ $item['no_pjax'] ?? 0 }}">
                        {!! str_repeat('&nbsp;', $depth) !!}
                        <p>
                            {!! $builder->translate($item['title']) !!}

                            @if(! $horizontal)
                                <i class="right fa fa-angle-left"></i>
                            @endif
                        </p>
                    </a>
                    <ul class="nav {{ $horizontal ? 'dropdown-menu' : 'nav-treeview' }}">
                        @foreach($item['children'] as $item)
                            @php
                                $item['depth'] = $depth + 1;
                            @endphp

                            @include('admin::partials.menu', ['item' => $item])
                        @endforeach
                    </ul>
                </li>
            @endif
        @endif
    @endforeach
</ul>
