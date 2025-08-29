<div class="list-group">
    @foreach($posts as $post)
        <x-post :post="$post"/>
    @endforeach
    <div class="d-flex justify-content-between align-items-center">
        <div>
            Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} posts
        </div>
        <ul class="pagination">
            @if (!$posts->onFirstPage())
                <li class="page-item">
                    <button wire:click="previousPage" class="page-link"><</button>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link"><</span>
                </li>
            @endif
            @for ($i = 1; $i <= $posts->lastPage(); $i++)
                @if ($i == $posts->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $i }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <button wire:click="gotoPage({{ $i }})" class="page-link">{{ $i }}</button>
                    </li>
                @endif
            @endfor
            @if ($posts->hasMorePages())
                <li class="page-item">
                    <button wire:click="nextPage" class="page-link">></button>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">></span>
                </li>
            @endif
        </ul>
    </div>
</div>



