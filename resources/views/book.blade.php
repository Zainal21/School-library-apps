@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><span>Book Management</span>
                  @if (auth()->user()->role == 1)
                    <button id="add_books" class="btn btn-primary float-right">Add Book</button>
                   @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-stripped" id="table_books">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>BookCode</th>
                                    <th>Title</th>
                                    <th>Authors</th>
                                    <th>Total Page</th>
                                    <th>Description</th>
                                    <th>Published date</th>
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
<div class="modal fade" tabindex="-1" role="dialog" id="modal-books">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-cateory-title">Book Form</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" class="form-group" id="form-book">
                    <input type="hidden" name="id" id="id" class="form-control">
                    <div class="form-group">
                        <label for="book_code">Book Code</label>
                        <input type="text" name="book_code" id="book_code" class="form-control" value="{{$next_bookcode}}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="author">Authors</label>
                        <select name="authors[]" class="form-control" id="authors" multiple="multiple"
                            style="width:100%;"></select>
                    </div>
                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <input type="number" min="0" max="10" name="rating" id="rating" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="total_page">Total Page</label>
                        <input type="number" name="page" id="page" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="30" rows="10"
                            class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="published_date">Published Date</label>
                        <input type="date" name="published_date" id="published_date" class="form-control">
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
        showbooks();
        $('#add_books').on('click', function () {
            resetForm()
            $('#modal-books').modal('show')
        })

        $('#authors').select2({
            allowClear: true,
            placeholder: "Choose the author",
            multiple: true,
            ajax: {
                url: "{{route('author.findByName')}}",
                type: 'GET',
                data: ({
                    term
                }) => ({
                    keyword: term
                }),
                processResults: data => ({results: data.results})
            }
        })

        $('#form-book').on('submit', function (e) {
            e.preventDefault();
            let id = $('#id').val();
            let route = (id != '') ? `{{url('api/book/`+ id +`')}}` :  "{{route('book.store')}}";
            let method =  (id != '') ? 'patch' : 'post';
            ajaxRequest($(this).serialize(),route,method)
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

    const showbooks = () => {
        const columns = [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
            },
            {
                data: 'book_code',
                name: 'book_code'
            },
            {
                data: 'title',
                name: 'title'
            },
            {
                data: 'authors',
                name: 'authors'
            },
            {
                data: 'page',
                name: 'page'
            },
            {
                data: 'description',
                name: 'description'
            },
            {
                data: 'published_date',
                name: 'published_date'
            },
            {
                data: 'action',
                name: 'action'
            }
        ]
        showDataTable('#table_books', "{{route('book.get_book')}}", columns)
    }

    const actionDeleteBook = (id) => {
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
                ajaxRequest(null, `{{url('api/book/` + id + `')}}`, 'delete')
                    .then(({
                        message
                    }) => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: message,
                            icon: 'success',
                        }).then(() => {
                            resetForm();
                            showbooks()
                            $('#modal-books').modal('hide')
                        })
                    })
                    .catch((e) => console.log(e))
            }
        })

    }

    const actionShowBookDetail = (id) => {
        ajaxRequest(null, `{{url('api/book/` + id + `')}}`, 'get')
            .then(({
                results
            }) => {
                $('#authors').html(null).trigger('change');
                $('#id').val(results.id)
                $('#page').val(results.page)
                $('#title').val(results.title)
                $('#description').val(results.description)
                $('#book_code').val(results.book_code)
                $('#published_date').val(results.published_date)
                $('#rating').val(results.rating)
                $('#modal-books').modal('show')
                 results.authors.map(({text, id}) => {
                    const optionChoice = new Option(text, id, true, true)
                    $('#authors').append(optionChoice).trigger('change')
                })
            })
            .catch((e) => console.log(e))
    }

    const resetForm = () => {
        $('#id').val('')
        $('#page').val('')
        $('#title').val('')
        $('#description').val('')
        $('#published_date').val('')
        $('#rating').val('')
        $('#book_code').val('{{$next_bookcode}}');
        $('#authors').html(null).trigger('change');
    }

</script>
@endpush
