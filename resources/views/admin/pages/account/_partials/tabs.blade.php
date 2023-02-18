<ul class="nav nav-pills flex-column flex-md-row mb-4">
    <li class="nav-item"><a class="nav-link {{ Request::query('t') === 'general' ? 'active' : '' }}" href="{{ route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'general'])}}"><i class="ti-xs ti ti-users me-1"></i> Account</a></li>
    <li class="nav-item"><a class="nav-link {{ Request::query('t') === 'security' ? 'active' : '' }}" href="{{ route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])}}"><i class="ti-xs ti ti-lock me-1"></i> Security</a></li>
    <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-billing')}}"><i class="ti-xs ti ti-file-description me-1"></i> Billing & Plans</a></li>
    <li class="nav-item"><a class="nav-link {{ Request::query('t') === 'notifications' ? 'active' : '' }}" href="{{ route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'notifications'])}}"><i class="ti-xs ti ti-bell me-1"></i> Notifications</a></li>
    <li class="nav-item"><a class="nav-link {{ Request::query('t') === 'authlogs' ? 'active' : '' }}" href="{{ route('admin.auth-logs', ['t' => 'authlogs'])}}"><i class="ti-xs ti ti-key me-1"></i> Authentication Logs</a></li>
    <li class="nav-item"><a class="nav-link" href="{{url('pages/account-settings-connections')}}"><i class="ti-xs ti ti-link me-1"></i> Connections</a></li>
</ul>