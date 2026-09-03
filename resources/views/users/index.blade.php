@extends('layouts.app')

@section('title', 'Users - Email Services')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1>Manage Users</h1>
            <p class="subtitle mb-0">Add, update, or remove dashboard users.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary">Add User</a>
    </div>


    <div class="card p-0 shadow-none">
        <div class="table-wrapper border-none rounded-xl">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($user->image)
                                        <img src="{{ Storage::url($user->image) }}" alt="" class="w-8 h-8 rounded-full object-cover border border-hairline">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-surface border border-hairline flex items-center justify-center text-steel font-medium text-xs">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-ink">{{ $user->name }}</div>
                                        @if($user->username)
                                            <div class="text-[12px] text-steel">{{ '@' . $user->username }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge success">Active</span>
                                @else
                                    <span class="badge" style="background: var(--surface); color: var(--steel);">Inactive</span>
                                @endif
                            </td>
                            <td class="text-steel">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn-ghost px-2 py-1 text-[13px] border border-hairline">Edit</a>
                                    
                                    @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="m-0 form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost px-2 py-1 text-[13px] border border-hairline text-[#d45656]">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    No users found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.form-delete');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d45656',
                    cancelButtonColor: '#888888',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
