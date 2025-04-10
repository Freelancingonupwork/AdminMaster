<!-- <div class="modal fade" id="myModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"> -->
<div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content" style="background-color: lightslategray;">
        <div class="modal-header">
            <h5 class="modal-title" id="myModal">Subscription</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <?php
                    use App\Helper\Helper;
                    $planData = Helper::plans();
                ?>
                @foreach($planData as $plan)
                <div class="col-sm-4">
                    <div class="position-relative p-3 bg-gray" style="height: 350px">
                        <div class="ribbon-wrapper ribbon-lg">
                            <div class="ribbon bg-info">
                                New
                            </div>
                        </div>
                        <div class="plan-title">
                            {{ $plan['name'] }}
                        </div>
                        <div class="plan-desc">

                            {!! $plan['description'] !!}
                        </div>
                        <strong class="text pl-5">Only {!! $plan['price'] !!}</strong>
                        <div class="plan-image mt-2 mb-2 pl-5">

                            @if(!empty($plan['image']))
                            <img src="{{ asset('uploads/plans/'. $plan['image']) }}" style="width: 150px; height:100px;" />
                            @else
                            <img src="{{ asset('uploads/cms/cms-dummy-image.png') }}" style="width: 150px; height:100px;" />
                            @endif
                        </div>
                        <div class="subscribe-btn pl-5">
                            <form action="{{ url('/subscribe') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="plan_name" value="{{ $plan['name'] }}">
                                <input type="hidden" name="plan_value" value="{{ $plan['price'] }}">
                                <input type="hidden" name="plan_type" value="{{ $plan['plan_type'] }}">
                                <button type="submit" class="btn btn-success btn-sm">Select</a>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
</div>