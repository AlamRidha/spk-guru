@foreach (config('adminlte.menu') as $item)
    @if (isset($item['role']) && $item['role'] !== auth()->user()->role)
        @continue
    @endif
    <li class="nav-item @if (Request::is($item['active'])) active @endif">
        <a href="{{ route($item['route']) }}" class="nav-link">
            <i class="nav-icon {{ $item['icon'] }}"></i>
            <p>{{ $item['text'] }}</p>
        </a>
    </li>
@endforeach
