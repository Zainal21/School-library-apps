@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><span>Book Creator Management</span>
                    @if (auth()->user()->role == 1)
                     <button id="add_authors" class="btn btn-primary float-right">Add New Creator</button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-stripped" id="table_authors">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Author Name</th>
                                    <th>Date Of Birth</th>
                                    <th>Short Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-authors">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-cateory-title">Creator Form</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" class="form-group" id="form-author">
                    <input type="hidden" name="id" id="id" class="form-control">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="short_description">Short Description</label>
                        <textarea name="short_description" id="short_description" cols="30" rows="10" class="form-control"></textarea>
                    </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        showAuthors();
        $('#add_authors').on('click', function () {
            resetForm()
            $('#modal-authors').modal('show')
        })

        $('#form-author').on('submit', function (e) {
            e.preventDefault();
            let id = $('#id').val();
            let route = (id != '') ? `{{url('api/author/`+ id +`')}}` :  "{{route('author.store')}}";
            let method =  (id != '') ? 'patch' : 'post';
            ajaxRequest($(this).serialize(),route, method)
                .then(({
                    message
                }) => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: message,
                        icon: 'success',
                    }).then(() => {
                        resetForm();
                        showAuthors()
                        $('#modal-authors').modal('hide')
                    })
                }).catch((e) => {
                    if (typeof (e.message) == 'object') {
                        $.each(e.message, function (key, value) {
                            toastr.error(value, 'Kesalahan Input Data!')
                        });
                    } else {
                        toastr.error(e.message, 'Kesalahan Input Data!')
                    }

                })
        })
    })

    const showAuthors = () => {
        const columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
            },
            {
                data: 'fullname',
                name: 'fullname'
            },
            {
                data: 'date_of_birth',
                name: 'date_of_birth'
            },
            {
                data: 'short_description',
                name: 'short_description'
            },
            {
                data: 'action',
                name: 'action'
            }
        ]
        showDataTable('#table_authors', "{{route('author.get_author')}}", columns)
    }

    const actionDeleteAuthor = (id) => {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "untuk menghapus data tersebut!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                ajaxRequest(null, `{{url('api/author/` + id + `')}}`, 'delete')
                    .then(({
                        message
                    }) => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: message,
                            icon: 'success',
                        }).then(() => {
                            window.location.reload()
                        })
                    })
                    .catch((e) => console.log(e))
            }
        })

    }

    const actionShowAuthorDetail = (id) => {
        ajaxRequest(null, `{{url('api/author/` + id + `')}}`, 'get')
            .then(({
                results
            }) => {
                $('#first_name').val(results.first_name)
                $('#last_name').val(results.last_name)
                $('#date_of_birth').val(results.date_of_birth)
                $('#short_description').html(results.short_description)
                $('#id').val(results.id)
                $('#modal-authors').modal('show')
            })
            .catch((e) => console.log(e))
    }

    const resetForm = () => {
        $('#id').val('')
        $('#first_name').val('')
        $('#last_name').val('')
    }

</script>
@endpush
