
<!--I believe this is redundant.  I.e. I duplicated this func in post-list.blade.php and its controller PostList.php
<div class="d-flex justify-content-end">
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
