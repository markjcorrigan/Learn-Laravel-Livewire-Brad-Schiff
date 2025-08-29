<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}}'s Profile">
    <livewire:post-list :username="$sharedData['username']" />
</x-profile>

