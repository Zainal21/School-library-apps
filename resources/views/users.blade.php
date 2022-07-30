@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><span>Users Management</span>
                    <button id="add_Users" class="btn btn-primary float-right">Add Users</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-stripped" id="table_users">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>email</th>
                                    <th>username</th>
                                    <th>role</th>
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
<div class="modal fade" tabindex="-1" role="dialog" id="modal-Users">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-cateory-title">User Form</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" class="form-group" id="form-users">
                    <input type="hidden" name="id" id="id" class="form-control">
                    <div class="form-group">
                        <label for="name">Username</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="Password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" autocomplete="false">
                        <small class="text-muted">* fill password when reset or add new user (if the password is not entered when <strong> add new user</strong>, it will automatically become <strong>Password123</strong>)</small>
                    </div>
                    <div class="form-group">
                        <label for="Role">Role</label>
                        <select name="role" id="role" class="form-control">
                            <option value="">Choose A Role</option>
                            <option value="1">Admin</option>
                            <option value="2">Non-Admin</option>
                        </select>
                    </div>

            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="btn-save-users" class="btn btn-primary">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        showUsers();
        $('#add_Users').on('click', function () {
            resetForm()
            $('#btn-save-users').removeClass('collapse')
            $('#modal-Users').modal('show')
        })

        $('#form-users').on('submit', function (e) {
            e.preventDefault();
            let id = $('#id').val();
            let route = (id != '') ? `{{url('api/users/` + id + `')}}` : "{{route('users.store')}}";
            let method = (id != '') ? 'patch' : 'post';
            ajaxRequest($(this).serialize(), route, method)
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

    const showUsers = (datafilter = false) => {
        const columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
            },
            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'role',
                name: 'role'
            },
            {
                data: 'action',
                name: 'action'
            }
        ]
        showDataTable('#table_users', "{{route('users.get_users')}}", columns, datafilter)
    }

    const actionDeleteUsers = (id) => {
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
                ajaxRequest(null, `{{url('api/users/` + id + `')}}`, 'delete')
                    .then(({
                        message
                    }) => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: message,
                            icon: 'success',
                        }).then(() => {
                            resetForm();
                            showUsers()
                            $('#modal-Users').modal('hide')
                        })
                    })
                    .catch((e) => console.log(e))
            }
        })

    }

    const resetForm = () => {
        $('#id').val('')
        $('#name').val('')
        $('#email').val('')
        $('#role').val('').trigger('change')
    }

</script>
@endpush
