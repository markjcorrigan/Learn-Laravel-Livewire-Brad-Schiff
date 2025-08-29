<x-layout doctitle="Manage Your Avatar">
  <div class="container container--narrow py-md-5">
    <h2 class="text-center mb-3">Upload a New Avatar</h2>
    <livewire:avatarupload />
  </div>
</x-layout>


{{-- <x-layout doctitle="Manage your avatar">
    <div class="container container--narrow py-md-5">
        <h2 class="text-center mb-3">Upload a new avatar</h2>
        <form method="POST" action="/manage-avatar" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <input type="file" name="avatar" required>
                @error('avatar')
                <p class="small alert alert-danger shadow-sm">
                    {{ $message }}
                </p>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</x-layout> --}}
