@extends('layout.parent')

@section('title', $action." Part")

@section('main')

    <div class="pagetitle">
        <a href="{{route('parts')}}" class="btn btn-outline-secondary float-end"><i class="bi bi-arrow-left-circle me-2"></i>Go Back</a>
        <h1>Parts</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('parts')}}">Parts</a></li>
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
                        <h5 class="card-title">{{$action}} Part</h5>
                        <form method="POST" action="@if($action==="Add"){{ route('parts.store') }}@else{{ route('parts.update',$data->id) }}@endif" class="row g-3 needs-validation" novalidate>
                            @csrf
                            @if($action==="Edit") @method('PUT') @endif
                            @if(isset($data))<input hidden type="text" name="id" value="{{$data->id}}">@endif
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="part_title" class="form-control" id="part-title" @if(isset($data)) value="{{$data->part_title}}" @endif placeholder="Part Title*" required>
                                    <label for="part-title">Part Title*</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid part title.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="part_no" class="form-control" id="part-no" @if(isset($data)) value="{{$data->part_no}}" @endif placeholder="Part No*" required>
                                    <label for="floatingName">Part No*</label>
                                    <div class="invalid-feedback">
                                        Please provide a valid part no.
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button class="btn btn-success" type="submit"><i class="bi bi-check-circle me-2"></i> Save</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
