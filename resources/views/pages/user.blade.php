@extends('app')

@section('content')
    <script src="https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>

    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-3">
                <select name="FilterRole" id="FilterRole" class="form-control" onchange="refreshTable()">
                    <option value="">Semua Role</option>
                    <option value="Pusat">SQ</option>
                    <option value="Smw">SMW</option>
                    <option value="Outlet">OUTLET</option>
                </select>
            </div>
            <div class="col-3">
                <select name="FilterWilayah" id="FilterWilayah" class="form-control">
                    <option value="">Semua Wilayah</option>
                    @foreach ($wilayah as $key)
                        <option value="{{ $key->wilayah }}">{{ $key->wilayah }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3">
                <select name="FilterCabang" id="FilterCabang" class="form-control">
                    <option value="">Semua Cabang</option>
                </select>
            </div>
            <div class="col-3">
                <select name="FilterOutlet" id="FilterOutlet" class="form-control" onchange="refreshTable()">
                    <option value="">Semua Outlet</option>
                </select>
            </div>
            <div class="col-12 mt-5 mb-5">
                <a class="btn btn-primary float-end" href="https://elvis-premises.online/auth/register">
                    Tambah user
                </a>
            </div>
            <div class="col-12">
                <table class="table table-bordered" id="userTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Role</th>
                            <th scope="col">Wilayah</th>
                            <th scope="col">Cabang</th>
                            <th scope="col">Outlet</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="detailModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-sm-fullscreen">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="staticBackdropLabel">Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body border-0">
                    <form action="">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <br>
                                <div class="form-group mb-5 d-flex align-items-center justify-content-center gap-3">
                                    <img src="https://www.imgacademy.com/themes/custom/imgacademy/images/helpbox-contact.jpg"
                                        alt="thumbnail" class="rounded-circle"
                                        style="width: 100px;height: 100px; object-fit: cover;" id="img">
                                    <div class="form-group d-none">
                                        <input type="file" class="form-control">
                                    </div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <label for="nama">Nama</label>
                                    <input type="text" class="form-control" id="nama" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="hp">Phone</label>
                                    <input type="text" class="form-control" id="hp" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-control" id="email" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="detailRole">Role</label>
                                    <Select id="detailRole" name="detailRole" class="form-control" disabled>
                                        <option value="Pusat">SQ</option>
                                        <option value="Smw">SMW</option>
                                        <option value="Outlet">OUTLET</option>
                                    </Select>
                                </div>
                                <div class="form-group">
                                    <label for="detailWilayah">Wilayah</label>
                                    <input type="text" id="detailWilayah" name="detailWilayah" class="form-control"
                                        disabled>
                                </div>
                                <div class="form-group">
                                    <label for="detailCabang">Cabang</label>
                                    <input type="text" id="detailCabang" name="detailCabang" class="form-control"
                                        disabled>
                                </div>
                                <div class="form-group">
                                    <label for="detailOutlet">Outlet</label>
                                    <input type="text" id="detailOutlet" name="detailOutlet" class="form-control"
                                        disabled>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Sembunyikan</button>
                    <button type="button" class="btn btn-danger d-none">Edit</button>
                    <button type="button" class="btn btn-warning d-none">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="/assets/css/datatable.css">
@endsection


@section('script')
    <script>
        //--------- Datatable
        var table = $('#userTable').DataTable({
            processing: true,
            serverSide: true,
            lengthMenu: [
                [5, 10, 20, -1],
                [5, 10, 20, "All"]
            ],
            responsive: true,
            ajax: {
                url: '/user/load_user',
                data: function(d) {
                    d.role = $("#FilterRole").val();
                    d.wilayah = $("#FilterWilayah").val();
                    d.cabang = $("#FilterCabang").val();
                    d.outlet = $("#FilterOutlet").val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'email'
                },
                {
                    data: 'hp'
                },
                {
                    data: 'role'
                },
                {
                    data: 'wilayah'
                },
                {
                    data: 'cabang'
                },
                {
                    data: 'outlet'
                },
                {
                    data: 'action'
                },
            ],
        });

        function refreshTable() {
            table.draw();
        }

        $('#FilterWilayah, #addWilayah').change(function(e) {
            e.preventDefault();
            var wilayah = $(this).val();
            load_cabang(wilayah);
        });

        $('#FilterCabang, #addCabang').change(function(e) {
            e.preventDefault();
            var cabang = $(this).val();
            load_outlet(cabang);
        });


        $('#addRole').change(function(e) {
            e.preventDefault();
            var role = $(this).val();

            if (role === 'Pusat') {
                $("#addWilayah").val("").prop("disabled", true);
                $("#addCabang").val("").prop("disabled", true);
                $("#addOutlet").val("").prop("disabled", true);
            } else if (role === 'Smw') {
                $("#addWilayah").prop("disabled", false);
                $("#addCabang").val("").prop("disabled", true);
                $("#addOutlet").val("").prop("disabled", true);
            } else {
                $("#addWilayah").prop("disabled", false);
                $("#addCabang").prop("disabled", false);
                $("#addOutlet").prop("disabled", false);
            }
        });

        function load_cabang(wilayah) {
            $.ajax({
                type: "POST",
                url: "{{ url('load_cabang') }}",
                data: {
                    wilayah: wilayah
                },
                dataType: "json",
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
                success: function(response) {
                    var html = '<option value="">Semua Cabang</option>';
                    response.forEach(element => {
                        html += `<option value="${element['cabang']}"> ${element['outlet']} </option>`
                    });

                    $("#FilterCabang").html(html);
                    $("#addCabang").html(html);

                    refreshTable();
                }
            });
        }

        function load_outlet(cabang) {
            $.ajax({
                type: "POST",
                url: "{{ url('load_outlet') }}",
                data: {
                    cabang: cabang
                },
                dataType: "json",
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
                success: function(response) {
                    console.log(response);
                    var html = '<option value="">Semua Outlet</option>';
                    response.forEach(element => {
                        html += `<option value="${element['outlet']}"> ${element['outlet']} </option>`
                    });

                    $("#FilterOutlet").html(html);
                    $("#addOutlet").html(html);
                    refreshTable();
                }
            });
        }
    </script>

    <script>
        $('#addFoto').change(function() {
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    console.log(event.target.result);
                    $('#preview-add').attr('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>

    <script>
        $(document).on('click', '.btn-delete', function() {

            var userId = $(this).attr('data-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Ketik Password',
                        input: 'text',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Konfirmasi',
                        showLoaderOnConfirm: true,
                        preConfirm: (password) => {
                            $.ajax({
                                type: "POST",
                                url: "/password/confirmation",
                                data: {
                                    password: password,
                                    userId: userId
                                },
                                dataType: "json",
                                headers: {
                                    'X-CSRF-Token': '{{ csrf_token() }}',
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                    ).then(function(param) {
                                        table.draw();
                                    })
                                },
                                error: function(response) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Something went wrong!',
                                    })
                                }
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    })
                }
            })
        })

        const formField = ['nama', 'hp', 'email', 'detailRole', 'detailWilayah', 'detailCabang', 'detailOutlet'];
        const detailField = ['nama', 'hp', 'email', 'role', 'wilayah', 'cabang', 'outlet'];
        var base_url = "https://elvis-premises.online/";

        $(document).on('click', '.btn-detail', function() {
            $("#detailModal").modal('show');
            var id = $(this).attr('data-id');
            $.ajax({
                type: "POST",
                url: "/user/get_user",
                data: {
                    id: id
                },
                dataType: "json",
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}',
                },
                success: function(response) {
                    formField.forEach((element, index) => {
                        if (response[detailField[index]]) {
                            $(`#${element}`).parent().removeClass('d-none');
                            $(`#${element}`).val(response[detailField[index]]);
                        } else {
                            $(`#${element}`).parent().addClass('d-none');
                        }
                    });

                    if (response.foto) {
                        $("#img").attr("src", base_url + "img_profile/" + response.foto);
                    }
                }
            });
        })
    </script>
@endsection
