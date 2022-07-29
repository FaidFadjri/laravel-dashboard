@extends('app')

@section('content')
    <script src="https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>

    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-12">
                {{-- Input Hidden for custom parameter --}}
                <input type="hidden" class="form-control" id="parameter_premises"
                    value="{{ isset($premises) ? $premises : '' }}" readonly>
                <input type="hidden" class="form-control" id="parameter_kondisi" value="{{ isset($kondisi) ? $kondisi : '' }}"
                    readonly>
                <input type="hidden" class="form-control" id="parameter_wilayah"
                    value="{{ isset($wilayah) ? $wilayah : '' }}" readonly>
            </div>
            <div class="col-12">
                <input type="date" class="form-control rounded border-0" id="date">
            </div>
            {{-- <div class="col-3">
                <select class="form-select border-0" aria-label="Default select example">
                    <option selected>Semua Wilayah</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div> --}}
            {{-- <div class="col-3">
                <select class="form-select border-0" aria-label="Default select example">
                    <option selected>Semua Cabang</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div>
            <div class="col-3">
                <select class="form-select border-0" aria-label="Default select example">
                    <option selected>Semua Outlet</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div> --}}
            <div class="col-12 mt-4">
                <table class="table table-bordered nowrap" style="width:100%" id="checksheetTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Wilayah</th>
                            <th>Cabang</th>
                            <th>Outlet</th>
                            <th>Premises</th>
                            <th>Kategori</th>
                            <th>Penilaian</th>
                            <th>Status</th>
                            <th>Outlet Submit Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="evidenceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body border-0 text-center">
                    <img src="https://www.astralife.co.id/beta/wp-content/uploads/2019/11/default-img.png" class="img-fluid"
                        alt="evidence" id="evidence-review" style="max-height: 60vh; object-fit: contain">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Sembunyikan</button>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="/assets/css/datatable.css">
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#checksheetTable').DataTable({
                processing: true,
                serverSide: true,
                lengthMenu: [
                    [5, 10, 20, -1],
                    [5, 10, 20, "All"]
                ],
                responsive: true,
                ajax: {
                    url: '/load_datatable',
                    data: function(d) {
                        d.date = $('#date').val();
                        d.parameter_premises = $("#parameter_premises").val();
                        d.parameter_kondisi = $("#parameter_kondisi").val();
                        d.parameter_wilayah = $("#parameter_wilayah").val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex'
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
                        data: 'premises'
                    },
                    {
                        data: 'kategori'
                    },
                    {
                        data: 'kondisi'
                    },
                    {
                        data: 'verifikasi'
                    },
                    {
                        data: 'submitDate'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });

            $('#date').change(function(e) {
                e.preventDefault();
                table.draw();
            });
        });

        $(document).on('click', '.btn-detail', function(param) {
            $('#evidenceModal').modal('show');

            var id = $(this).attr('data-id');

            $.ajax({
                type: "get",
                url: "/get_detail",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    var data = response.data;
                    if (data) {
                        if (data.img) {
                            $('#evidence-review').attr('src',
                                `https://elvis-premises.online/assets/evidence/${data.img}`);
                        }
                    }
                }
            });
        })
    </script>
@endsection
