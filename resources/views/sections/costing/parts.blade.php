@extends('layout.parent')

@section('title', $action." Part Costing")

@section('main')

    <div class="pagetitle">
        <a href="{{route('costings.show',$data->id)}}" class="btn btn-outline-secondary float-end"><i
                class="bi bi-arrow-left-circle me-2"></i>Go Back</a>
        <h1>{{$action}} Part</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('costings')}}">Costing</a></li>
                <li class="breadcrumb-item"><a href="{{route('costings.show',$data->id)}}">{{$data->id}}</a></li>
                <li class="breadcrumb-item">Part</li>
                <li class="breadcrumb-item active">{{$action}}</li>
            </ol>
        </nav>
    </div>

    @include('components.alert')

    <section class="section">
        <div class="row ">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Part Costing</h5>
                            @if(isset($data->part))
                                <form method="POST" id="delete"
                                      action="{{route('costings.part.delete',["id"=>$data->id,"partId"=>$data->part->id])}}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i
                                            class="bi bi-trash me-2"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <span class="badge bg-primary me-2 fs-6"><b>Total Invoice:</b> ¥{{number_format($data->total_invoice_rmb,2)}}</span>
                            <span class="badge bg-warning me-2 fs-6"><b>Exchange Rate:</b> ¥{{number_format($data->exchange_rate,2)}}</span>
                            <span class="badge bg-danger me-2 fs-6"><b>Shipping Charges:</b> ¥{{number_format($data->shipping_charges,2)}}</span>
                            <span class="badge bg-secondary me-2 fs-6"><b>Total Freight:</b> Rs.{{number_format($data->total_freight,2)}}</span>
                            <span class="badge badge bg-dark me-2 fs-6"><b>Transpt. Charges in China:</b> Rs.{{number_format($data->transport_charge_china,2)}}</span>
                        </div>
                        <form method="POST"
                              action="@if($action==="New"){{ route('costings.part.store',$data->id) }}@else{{ route('costings.part.update',$data->id) }}@endif"
                              class="row g-3 needs-validation" novalidate>
                            @csrf
                            @if($action==="Edit")
                                @method('PUT')
                            @endif
                            @if(isset($data))
                                <input hidden type="text" name="id" value="{{$data->id}}">
                            @endif
                            @if(isset($data->part))
                                <input hidden type="text" name="old_part_id" value="{{$data->part->id}}">
                            @endif
                            <div class="col-sm-12 col-md-12">
                                <label for="unit-cost mb-2">Select Part*</label>
                                <div class="mb-3 gap-3 w-100 d-flex justify-content-around">
                                    <select class="part-search w-100" id="part-search" name="part" required>
                                        @foreach($data->parts as $part)
                                            <option value="{{ $part->id }}"
                                                    @if(isset($data->new_part)) @if($data->new_part===$part->id) selected
                                                    @endif @endif
                                                    @if(isset($data->part)) @if($data->part->id===$part->id) selected @endif @endif
                                            >
                                                {{ $part->title }} ({{ $part->part_no }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($action==="New")
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#addPartModal"
                                           class="btn btn-primary d-flex align-items-center"><i
                                                class="bi bi-plus-circle me-2"></i>New Part</a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input type="number" name="unit_cost" class="form-control calculate-costing"
                                           min="0.01" step="0.01" id="unit-cost" placeholder="Unit Cost (¥)*"
                                           @if(isset($data->part)) value="{{$data->part->unit_cost}}" @endif required>
                                    <label for="unit-cost">Unit Cost (¥)*</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid Unit Cost.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input type="number" name="quantity" class="form-control calculate-costing" min="1"
                                           step="1" id="quantity" placeholder="Quantity*"
                                           @if(isset($data->part)) value="{{$data->part->quantity}}" @endif required>
                                    <label for="quantity">Quantity*</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid quantity.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input type="number" name="margin" class="form-control calculate-costing" min="0.01"
                                           step="0.01" id="margin" placeholder="Margin (%)*"
                                           @if(isset($data->part)) value="{{$data->part->margin}}" @endif required>
                                    <label for="quantity">Margin (%)*</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid selling margin.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input type="number" name="selling_margin" class="form-control calculate-costing"
                                           min="0.01" step="0.01" id="selling-margin" placeholder="Selling Margin*"
                                           @if(isset($data->part)) value="{{$data->part->selling_margin}}"
                                           @endif required>
                                    <label for="quantity">Selling Margin*</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid selling margin.
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="selling_margin_percentage"
                                               id="selling-margin-percentage"
                                               @if(isset($data->part)) @if($data->part->selling_margin_percentage) checked @endif @endif>
                                        <label class="form-check-label" for="selling-margin-percentage">Is
                                            Percentage</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input readonly class="form-control-plaintext" id="total-rmb" value="-">
                                    <label for="exchange-rate">Total in RMB (¥)</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input readonly class="form-control-plaintext" id="total-lkr" value="-">
                                    <label for="exchange-rate">Total in LRK (Rs.)</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input readonly class="form-control-plaintext" id="freight-per-unit" value="-">
                                    <label for="exchange-rate">Freight Per Unit (Rs.)</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-3">
                                <div class="form-floating">
                                    <input readonly class="form-control-plaintext" id="cost-before-margin" value="-">
                                    <label for="exchange-rate">Cost before Margin (Rs.)</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input readonly class="form-control-plaintext fs-5" id="cost-after-margin"
                                           value="-">
                                    <label for="exchange-rate">Cost after Margin (Rs.)</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input readonly class="form-control-plaintext fs-5" id="selling-price" value="-">
                                    <label for="exchange-rate"><b>Selling Price (Rs.)</b></label>
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
        </div>
    </section>

    <!-- Add Part Modal -->
    <div class="modal fade" id="addPartModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Part</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('parts.store') }}" class="row g-3 needs-validation" novalidate>
                        @csrf
                        @if(isset($data))
                            <input hidden type="text" name="costing_id" value="{{$data->id}}">
                        @endif
                        <div class="col-sm-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" name="part_title" class="form-control" id="part-title"
                                       placeholder="Part Title*" required>
                                <label for="part-title">Part Title*</label>
                                <div class="invalid-feedback">
                                    Please provide a valid part title.
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="form-floating">
                                <input type="text" name="part_no" class="form-control" id="part-no"
                                       placeholder="Part No*" required>
                                <label for="floatingName">Part No*</label>
                                <div class="invalid-feedback">
                                    Please provide a valid part no.
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    <script>
        $(document).ready(function () {
            //Initialize part search box to select2
            $('#part-search').select2({
                placeholder: "Select a Part*"
            });

            //Calculate costings if editing
            @if($action==="Edit")
                calculateCostings();
            @endif

            $('.calculate-costing').keyup(function () {
                calculateCostings();
            });

            $('#selling-margin-percentage').on('change', function () {
                calculateCostings();
            });

            /**
             * Function to calculate costings of the entered parameter
             */
            function calculateCostings() {
                let unit_cost = $('#unit-cost').val();
                let quantity = $('#quantity').val();
                let margin = $('#margin').val();
                let selling_margin = $('#selling-margin').val();
                let selling_margin_percentage = $('#selling-margin-percentage').prop('checked');


                let exchangeRate = {{$data->exchange_rate}};
                let totalSumLKR = {{$data->total_sum_in_LKR}};
                let transportChargesChina = {{$data->transport_charge_china}};
                let totalFreight = {{$data->total_freight}};
                let totalRMB, totalLKR, freightPerUnit, costBeforeMargin, costAfterMargin, sellingPrice = 0;

                if (isValidNumber(unit_cost) && isValidNumber(quantity)) {
                    unit_cost = parseFloat(unit_cost);
                    quantity = parseFloat(quantity);
                    margin = parseFloat(margin);
                    selling_margin = parseFloat(selling_margin);

                    totalRMB = calculateTotalRMB(unit_cost, quantity);
                    totalLKR = calculateTotalLKR(totalRMB, exchangeRate);

                    freightPerUnit = calculateFreightPerUnit(transportChargesChina, totalFreight, totalLKR, totalSumLKR);

                    costBeforeMargin = calculateCostBeforeMargin(totalLKR, quantity, freightPerUnit);

                    $('#total-rmb').val(formatValue(totalRMB));
                    $('#total-lkr').val(formatValue(totalLKR));
                    $('#freight-per-unit').val(formatValue(freightPerUnit));
                    $('#cost-before-margin').val(formatValue(costBeforeMargin));
                }

                if (isValidNumber(margin) && costBeforeMargin !== 0 && margin > 0 && margin < 101) {
                    costAfterMargin = calculateCostAfterMargin(costBeforeMargin, margin);

                    $('#cost-after-margin').val(formatValue(costAfterMargin));
                }

                if (isValidNumber(selling_margin) && costAfterMargin !== 0) {
                    if (selling_margin_percentage && selling_margin < 1 && selling_margin > 101) {
                        return;
                    }
                    sellingPrice = calculateSellingPrice(costAfterMargin, selling_margin, selling_margin_percentage);

                    $('#selling-price').val(formatValue(sellingPrice));
                }
            }

            /**
             * Get the total of RMB by multiplying unit cost and quantity
             * @param unitCost
             * @param quantity
             * @returns {number}
             */
            function calculateTotalRMB(unitCost, quantity) {
                return unitCost * quantity;
            }

            /**
             * Get the total in LKR converted by multiplying total in RMB and exchange rate
             * @param totalRMB
             * @param exchangeRate
             * @returns {number}
             */
            function calculateTotalLKR(totalRMB, exchangeRate) {
                return totalRMB * exchangeRate;
            }

            /**
             * Get the freight per unit is calculated by multiplying the sum of transport charges
             * from China and total freight by the ratio of total LKR to total sum LKR.
             * @param transportChargesChina
             * @param totalFreight
             * @param totalLKR
             * @param totalSumLKR
             * @returns {number}
             */
            function calculateFreightPerUnit(transportChargesChina, totalFreight, totalLKR, totalSumLKR) {
                return (transportChargesChina + totalFreight) * (totalLKR / totalSumLKR);
            }

            /**
             * Cost before margin is the sum of the freight per unit and the average unit price.
             * @param totalLKR
             * @param quantity
             * @param freightPerUnit
             * @returns {*}
             */
            function calculateCostBeforeMargin(totalLKR, quantity, freightPerUnit) {
                return freightPerUnit + (totalLKR / quantity);
            }

            /**
             * Cost with margin is the cost before margin plus the cost before margin multiplied
             * by the margin percentage.
             * @param costBeforeMargin
             * @param margin
             * @returns {*}
             */
            function calculateCostAfterMargin(costBeforeMargin, margin) {
                return costBeforeMargin + (costBeforeMargin * (margin / 100));
            }

            /**
             * If margin is added as a percentage then the selling price is calculated by sum of
             * the cost after margin and the product of the cost after margin and the selling margin
             * divided by 100, if not the sum of margin and the cost after margin is considered.
             * @param costAfterMargin
             * @param selling_margin
             * @param selling_margin_percentage
             * @returns {*}
             */
            function calculateSellingPrice(costAfterMargin, selling_margin, selling_margin_percentage) {
                if (selling_margin_percentage) {
                    return costAfterMargin + (costAfterMargin * (selling_margin / 100));
                } else {
                    return costAfterMargin + selling_margin;
                }
            }

            /**
             * Check if the passing parameter is a valid number
             * @param value
             * @returns {boolean}
             */
            function isValidNumber(value) {
                let num = parseFloat(value);
                return !isNaN(num) && num !== 0;
            }

            /**
             * Format any passing parameter to two decimal places.
             * @param value
             * @returns {string}
             */
            function formatValue(value) {
                return value.toFixed(2);
            }
        });
    </script>
@endsection
