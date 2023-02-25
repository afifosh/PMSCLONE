<ul class="nav nav-pills flex-column flex-md-row mb-4">
    <li class="nav-item"><a class="nav-link {{ Request::query('t') === 'general' ? 'active' : '' }}" href="{{ route('user-account.edit', ['user_account' => auth()->id(), 't' => 'general'])}}"><i class="ti-xs ti ti-users me-1"></i> Account</a></li>
    <li class="nav-item"><a class="nav-link {{ Request::query('t') === 'security' ? 'active' : '' }}" href="{{ route('user-account.edit', ['user_account' => auth()->id(), 't' => 'security'])}}"><i class="ti-xs ti ti-lock me-1"></i> Security</a></li>
    <li class="nav-item"><a class="nav-link {{ Request::query('t') === 'notifications' ? 'active' : '' }}" href="{{ route('user-account.edit', ['user_account' => auth()->id(), 't' => 'notifications']) }}"><i class="ti-xs ti ti-bell me-1"></i> Notifications</a></li>
    <li class="nav-item"><a class="nav-link {{ Request::query('t') === 'authlogs' ? 'active' : '' }}" href="{{ route('user-account.edit', ['user_account' => auth()->id(), 't' => 'authlogs'])}}"><i class="ti-xs ti ti-key me-1"></i> Authentication Logs</a></li>
</ul>
