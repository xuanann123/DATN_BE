@extends('admin.layouts.master')
@section('content')
<div class="content">
    <div class="animated fadeIn">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <strong class="card-title">Data Table</strong>
                        <a class="btn-redirect-add" href="{{ route('.admincategories.create') }}">Add</a>
                    </div>
                    
                    <div class="card-body">
                        @if (session('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-error">
                                {{ session('error') }}
                            </div>
                        @endif
                        <table id="bootstrap-data-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Image</th>
                                    <th>Description</th>
                                    <th>Parent</th>
                                    <th>Is_active</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->slug }}</td>
                                        <td class="td-image">
                                            <img width="150px" src="{{ asset($category->image) }}" alt="">
                                        </td>
                                        <td class="td-position">{{ $category->description }}</td>

                                        <td>{{ $category->parent_id != null ? $category->parent->name : '' }}</td>

                                        <td>
                                            {{ $category->is_active == 1 ? 'On' : 'Off' }}
                                        </td>
                                        <td>
                                            <span class="action">
                                                <a href="{{ route('.admincategories.edit', ['category' => $category->id]) }}"><i class="menu-icon fa fa-edit"></i></a>
                                                <form action="{{ route('.admincategories.destroy', ['category' => $category->id]) }}" method="post">
                                                    @csrf
                                                    @method("DELETE")
                                                    <button onclick="return confirm('Are you sure ?')"><i class="menu-icon fa fa-trash"></i></button>
                                                </form>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div><!-- .animated -->
</div><!-- .content -->
@endsection

@section('style-libs')
    <link rel="stylesheet" href="{{ url('theme/admin/assets/css/lib/datatable/dataTables.bootstrap.min.css') }}">
    <style>
        .btn-redirect-add {
            border-radius: 3px;
        }
        td {
            align-content: center;
        }
        .td-image {
            text-align: center;
        }

        .alert {
            padding: 15px 10px;
        }

        .alert-success {
            color: green;
        }
        .alert-error {
            color: red;
        }
        .card-header {
            display: flex;
            justify-content: space-between;

            a {
                padding: 5px 10px;
                background-color: black;
                color:white;
                font-size: 15px;
            }
        }
        .action {
            display: flex;

            a {
                margin-top: 2px;
            }
            
            button {
                border:none;
                margin-left: 3px;
                background-color: transparent;
            }
        }
    </style>
@endsection

@section('script-libs')
<script src="{{ url('theme/admin/assets/js/lib/data-table/datatables.min.js') }}"></script>
<script src="{{ url('theme/admin/assets/js/lib/data-table/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ url('theme/admin/assets/js/lib/data-table/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('theme/admin/assets/js/lib/data-table/buttons.bootstrap.min.js') }}"></script>
<script src="{{ url('theme/admin/assets/js/lib/data-table/jszip.min.js') }}"></script>
<script src="{{ url('theme/admin/assets/js/lib/data-table/vfs_fonts.js') }}"></script>
<script src="{{ url('theme/admin/assets/js/lib/data-table/buttons.html5.min.js') }}"></script>
<script src="{{ url('theme/admin/assets/js/lib/data-table/buttons.print.min.js') }}"></script>
<script src="{{ url('theme/admin/assets/js/lib/data-table/buttons.colVis.min.js') }}"></script>
<script src="{{ url('theme/admin/assets/js/init/datatables-init.js') }}"></script>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {
      $('#bootstrap-data-table-export').DataTable();
  } );
</script>
@endsection