@extends('layout.parent')

@section('title', 'Parts')

@section('main')
    <div class="pagetitle">
        <a href="{{route('parts.add')}}" class="btn btn-secondary float-end"><i class="bi bi-plus-circle me-2"></i>Add Part</a>
        <h1>Parts</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active">Parts</li>
            </ol>
        </nav>
    </div>

    @include('components.alert')

    <section class="section">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">List - @if(isset($parts)){{$parts->count()}}@endif part(s)</h5>
                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Part Title</th>
                                    <th>Part No</th>
                                    <th>Created at</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($parts as $part)
                                    <tr>
                                        <td><a href="{{route('parts.show',$part->id)}}">{{ $part->title }}</a></td>
                                        <td>{{ $part->part_no }}</td>
                                        <td>{{ $part->created_at }}</td>
                                        <td><a class="btn btn-primary btn-sm"
                                               href="{{route('parts.show',$part->id)}}"><i class="bi bi-eye"></i></a>
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
