@extends('layout.parent')

@section('title', $action." Costing")

@section('main')

    <div class="pagetitle">
        <a href="{{route('costings')}}" class="btn btn-outline-secondary float-end"><i
                class="bi bi-arrow-left-circle me-2"></i>Go Back</a>
        <h1>Costing</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('costings')}}">Costing</a></li>
                <li class="breadcrumb-item active">{{$action}}</li>
            </ol>
        </nav>
    </div>

    @include('components.alert')

    <section class="section">
        @if(isset($data))
            <div class="row dashboard">
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Net Selling Price <span>| LKR</span></h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>Rs. {{$data->net_selling_price}}</h6>
                                    <span class="text-muted small pt-2 pe-1">Net profit:</span><span
                                        class="text-success small pt-1 fw-bold">Rs. {{$data->net_profit}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Parts <span>| (each.)</span></h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-gear-wide-connected"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{$data->parts->count()}}</h6>
                                    <span class="text-muted small pt-2 pe-1">Total quantity:</span><span
                                        class="text-primary small pt-1 fw-bold">{{$data->total_quantity}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Costing created <span>| {{$data->created_at_ago}}</span></h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{$data->created_at}}</h6>
                                    <span class="text-muted small pt-2 pe-1">Last updated</span><span
                                        class="text-danger small pt-1 fw-bold">{{$data->updated_at}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row ">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">General Costing</h5>
                        <form method="POST"
                              action="@if($action==="New"){{ route('costings.store') }}@else{{ route('costings.update',$data->id) }}@endif"
                              class="row g-3 needs-validation" novalidate>
                            @csrf
                            @if($action==="Edit")
                                @method('PUT')
                            @endif
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input type="number" name="total_invoice_rmb" min="0.01" step="0.01"
                                           class="form-control" id="total-invoice-rmb"
                                           @if(isset($data)) value="{{$data->total_invoice_rmb}}"
                                           @endif placeholder="Total Invoice RMB(¥)*" required>
                                    <label for="total-invoice-rmb">Total Invoice RMB(¥)*</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid total invoice RMB.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input type="number" name="exchange_rate" min="0.01" step="0.01"
                                           class="form-control" id="exchange-rate"
                                           @if(isset($data)) value="{{$data->exchange_rate}}"
                                           @endif placeholder="Exchange Rate*" required>
                                    <label for="exchange-rate">Exchange Rate*</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid exchange rate.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input type="number" name="shipping_charges" min="0.01" step="0.01"
                                           class="form-control" id="shipping-charges"
                                           @if(isset($data)) value="{{$data->shipping_charges}}"
                                           @endif placeholder="Shipping Charges (¥)*" required>
                                    <label for="shipping-charges">Shipping Charges (¥)*</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid shipping charges.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input type="number" name="total_freight" min="0.01" step="0.01"
                                           class="form-control" id="total-freight"
                                           @if(isset($data)) value="{{$data->total_freight}}"
                                           @endif placeholder="Total Freight (LKR)*" required>
                                    <label for="total-freight">Total Freight (LKR)*</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid total freight.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button class="btn btn-success" type="submit"><i class="bi bi-check-circle me-2"></i>
                                    Save
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            @if(isset($data))
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title d-flex justify-content-between align-items-center">Parts
                                <a href="{{route('costings.part.new',$data->id)}}" class="btn btn-warning"
                                   type="button"><i class="bi bi-plus-circle me-2"></i> Add Part</a>
                            </h5>
                            <div class="row">
                                @foreach($data->parts as $part)
                                    <div class="col-sm-12 col-md-3">
                                        <a href="{{route('costings.part.edit',["id"=>$data->id,"part_id"=>$part->id])}}">
                                            <div class="card parts-card">
                                                <div class="card-body">
                                                    <h6 class="card-title pb-0">{{$part->title}}
                                                        - {{$part->part_no}}</h6>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <span class="badge bg-primary me-2">[<b>Unit Cost:</b> ¥{{$part->unit_cost}}]</span>
                                                        <span class="badge bg-secondary me-2">[<b>Qty:</b> {{$part->quantity}}]</span>
                                                    </div>
                                                    <h5 class="mt-2 text-capitalize text-danger pb-0"><b>Selling
                                                            Price:</b> Rs.{{$part->selling_price}}</h5>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                                @if($data->parts->count()===0)
                                    <div class="col-sm-12">
                                        <p class="d-flex justify-content-center align-items-center text-capitalize text-danger text-center p-4">
                                            <i class="bi bi-exclamation-circle me-2 fs-2"></i>No parts in this costing
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection
