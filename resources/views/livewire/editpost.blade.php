<div>
    <form wire:submit.prevent="save">
        <div class="form-group">
            <label for="post-title" class="text-muted mb-1 d-block"><small>Title</small></label>
            <input wire:model="title" id="post-title" class="form-control form-control-lg form-control-title"
                type="text" placeholder="" autocomplete="off" />
            @error('title')
            <p class="m-0 small alert alert-danger shadow-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="post-body" class="text-muted mb-1"><small>Body Content</small></label>
            <textarea wire:model="body" required name="body" id="post-body"
                class="body-content tall-textarea form-control">{{ old('body', $post->body ?? '') }}</textarea>
            @error('body')
            <p class="mt-0 alert alert-danger shadow-sm"> {{ $message }} </p>
            @enderror
        </div>

        <div class="form-group">
            <label for="post-tags" class="text-muted mb-1 d-block"><small>Post Tags CSV: (Comma Separated
                    Values)</small></label>
            <input wire:model="post_tags" type="text" class="form-control @error('post_tags') is-invalid @enderror"
                placeholder="Tag1, Tag2 etc." value="{{ $post->post_tags }}">
            @error('post_tags')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label for="post-photo" class="text-muted mb-1 d-block"><small>Post Photo</small></label>
            <input wire:model="post_photo" @error('post_photo') is-invalid @enderror" type="file" id="Image">
            @error('post_photo')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            @if ($post_photo)
            <img src="{{ $post_photo->temporaryUrl() }}" alt="" style="width: 90px; height: 90px">
            @else
            <img src="{{ asset($post->photo) }}" alt="" style="width: 90px; height: 90px">
            @endif
        </div>

        <button class="btn btn-primary" type="submit">Save Changes</button>

    </form>
    <!-- <script>
        $('.summernote').summernote({
            placeholder: 'Hello stand alone ui',
            tabsize: 2,
            height: 120,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: function(contents, $editable) {
                    @this.set('body', contents);
                }
            }
        });
    </script>
    <script>
        document.getElementById('reload').addEventListener('click', function() {
            location.reload();
        });
    </script> -->
</div>