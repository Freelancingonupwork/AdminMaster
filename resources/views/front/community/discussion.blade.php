@extends('front.frontLayout.master')

@section('styles')
<style>
    .alert {
        border-radius: 1.25rem;
    }

    .alert-dismissible .btn-close {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 2;
        padding: 1rem 1rem;
    }

    p {
        color: #000000 !important;
        padding-left: 10px;
    }
</style>
<!-- <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.css"> -->
@endsection

@section('content')

<div class="row">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (Session::has('success'))
    <div class="alert alert-success" style="height:50px; border-radius:20px;">
        <a href="#" class="close" data-dismiss="alert" aria-label="close" style="text-decoration: none; height:50px; border-radius:0.75px;">x</a>
        <p>{{ Session::get('success') }}</p>
    </div>
    @endif

    @if (Session::has('error'))
    <div class="alert alert-danger" style="height:50px; border-radius:20px;">
        <strong>{{ Session::get('error') }}</strong>
        <a href="#" class="close" data-dismiss="alert" aria-label="close" style="text-decoration: none;">x</a>
    </div>
    @endif

    <div class="col-sm-12 p-2 mt-3">
        <div class="card featured-cards" style="border-radius: 15px; background-color:grey;">
            <div class="card-body">
                <div class="col-lg p-3">
                    <div class="row">
                        <div class="col-lg-1">
                            <img src="{{ asset('storage/'.$discussionsAddedBy->avatar) }}" style="border-radius:50px;height:65;width:65px" />
                        </div>
                        @foreach ($discussionData as $discussion)
                        <div class="col-lg">
                            <div class="col-lg-10" style="font-size:x-large; color:#fff">
                                {{ $discussion['topic'] }}
                            </div>
                            {!! $discussion['content'] !!}
                            <form action="{{ route('reply') }}" method="post" onsubmit="return validateForm()">
                                {{ csrf_field() }}
                                <div class="col-lg">
                                    <input type="hidden" name="topic_id" id="topic_id" value="{{ $discussion['id'] }}">
                                    <textarea class="form-control discussion" name="discussion_reply" id="discussion_reply" placeholder="Answer"></textarea>
                                    <button type="submit" id="submitDiscussion" class="btn btn-success btn-sm float-right mt-2">Submit</button>
                                </div>

                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#discussion_reply'))
        .catch(error => {
            console.error(error);
        });
</script> -->

<script type="text/javascript">

    function validateTextarea(textarea) {
        // Make sure the textarea is not empty
        if (textarea.length == 0) {
            return "Answer cannot be empty";
        }

        // Check the length of the textarea
        if (textarea.length > 1000) {
            return "Answer must be 1000 characters or fewer";
        }
        if (textarea.length < 10) {
            return "Answer must be at least 10 characters long";
        }

        // Make sure the textarea does not contain any invalid characters
        if (textarea.match(/[<>]/)) {
            return "Answer cannot contain < or > characters";
        }

        // Textarea is valid
        return "";
    }

    function validateForm() {
        // Get the form values
        var message = document.getElementById("discussion_reply").value;

        // Validate the textarea
        var messageError = validateTextarea(message);
        if (messageError) {
            alert(messageError);
            return false;
        }

        // Form is valid
        // alert("Form submitted successfully!");
        return true;
    }
</script>
@endsection
