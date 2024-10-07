@extends('layout.parent')

@section('title', 'Costing')

@section('main')
    <div class="pagetitle">
        <a href="{{route('costings.new')}}" class="btn btn-secondary float-end"><i class="bi bi-plus-circle me-2"></i>New
            Costing</a>
        <h1>Costing</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active">Costing</li>
            </ol>
        </nav>
    </div>

    @include('components.alert')

    <section class="section">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">List - @if(isset($costings))
                                {{$costings->count()}}
                            @endif costing(s)</h5>
                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Created at</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($costings as $costing)
                                <tr>
                                    <td><a href="{{route('costings.show',$costing->id)}}">{{ $costing->id }}</a></td>
                                    <td>{{ $costing->created_at }}</td>
                                    <td><a class="btn btn-primary btn-sm"
                                           href="{{route('costings.show',$costing->id)}}"><i class="bi bi-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
