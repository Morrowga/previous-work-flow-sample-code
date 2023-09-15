@extends(config('laravel-h5p.layout'))
<style>
    .h5p-video-wrapper {
        position: relative;
        width: 640px;
        /* Adjust this according to your video's dimensions */
    }

    video {
        width: 100%;
        height: auto;
    }

    .timeline {
        width: 100%;
        background-color: #ccc;
        height: 5px;
        cursor: pointer;
        position: relative;
    }

    .thumbnails {
        display: none;
        position: absolute;
        bottom: 100%;
        left: 0;
        width: 100%;
        text-align: center;
    }

    .thumbnail {
        display: inline-block;
        width: 50px;
        /* Adjust the width of thumbnails as needed */
        margin: 5px;
        cursor: pointer;
    }
</style>
@section('h5p')
    <div class="container-fluid">

        <div class="row">

            <div class="col-md-12">

                <div class="h5p-content-wrap">
                    {!! $embed_code !!}
                </div>

                <br />
                <p class='text-center'>
                    <a href="{{ url()->previous() }}" class="btn btn-default"><i class="fa fa-reply"></i>
                        {{ trans('laravel-h5p.content.cancel') }}</a>
                </p>
            </div>

        </div>

    </div>
@endsection

@push('h5p-header-script')
    {{--    core styles       --}}
    @foreach ($settings['core']['styles'] as $style)
        {{ Html::style($style) }}
    @endforeach

    @foreach ($settings['loadedCss'] as $style)
        {{ Html::style($style) }}
    @endforeach
@endpush

@push('h5p-footer-script')
    <script type="text/javascript">
        H5PIntegration = {!! json_encode($settings) !!};
    </script>


    {{-- custom script  --}}

    {{-- end custom script  --}}


    {{--    core script       --}}
    @foreach ($settings['core']['scripts'] as $script)
        {{ Html::script($script) }}
    @endforeach

    @foreach ($settings['loadedJs'] as $script)
        {{ Html::script($script) }}
    @endforeach
    <script defer></script>
    <script>
        function handleSubmit(event) {
            console.log('Hello World Par kwar')
        }

        var submitButton = document.getElementById('h5p-interactive-video-endscreen-submit-button');
        // var submitButton = document.querySelector('.h5p-content');
        if (submitButton) { // Check if the element exists
            submitButton.addEventListener('click', handleSubmit);
        }
    </script>
@endpush
