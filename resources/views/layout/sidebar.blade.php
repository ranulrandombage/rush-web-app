<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link @if(!Str::contains(Route::current()->getName(),'dashboard')) collapsed @endif" href="{{route('dashboard')}}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(!Str::contains(Route::current()->getName(),'parts')) collapsed @endif" href="{{route('parts')}}">
                <i class="bi bi-gear-wide-connected"></i>
                <span>Parts</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link @if(!Str::contains(Route::current()->getName(),'costing')) collapsed @endif" href="{{route('costings')}}">
                <i class="bi bi-calculator-fill"></i>
                <span>Costing</span>
            </a>
        </li>
    </ul>

</aside><!-- End Sidebar-->
