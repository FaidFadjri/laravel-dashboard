@extends('app')

@section('content')
    <script src="https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <div class="container-fluid">

        <form action="/export" method="POST">
            @csrf
            <div class="row mt-4">
                <div class="col-12 text-end">
                    <button class="btn btn-primary" type="submit">Export Excel</button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    {{-- Input Hidden for custom parameter --}}
                    <input type="hidden" class="form-control" id="parameter_premises"
                        value="{{ isset($premises) ? $premises : '' }}" readonly>
                    <input type="hidden" class="form-control" id="parameter_kondisi"
                        value="{{ isset($kondisi) ? $kondisi : '' }}" readonly>
                    <input type="hidden" class="form-control" id="parameter_outlet"
                        value="{{ isset($outlet) ? $outlet : '' }}" readonly>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="form-group">
                        <label for="startDate">Sejak Tanggal</label>
                        <input type="date" id="startDate" name="startDate" class="form-control mt-1" required>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="form-group">
                        <label for="endDate">Sampai Tanggal</label>
                        <input type="date" id="endDate" name="endDate" class="form-control mt-1" required>
                    </div>
                </div>

                <div class="col-sm-12 mt-2 col-md-12">
                    <select name="wilayah" id="filterWilayah" class="form-control" required>
                        <?php $user = json_decode(session()->get('user')); ?>
                        @if ($user->role == 'Pusat')
                            <option value="">Export Wilayah</option>
                            @foreach ($wilayah as $item)
                                <option value="{{ $item->wilayah }}">{{ $item->wilayah }}</option>
                            @endforeach
                        @else
                            @foreach ($wilayah as $item)
                                <option value="{{ $item->wilayah }}">{{ $item->wilayah }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-12 mt-4">
                <table class="table table-bordered nowrap" style="width:100%" id="checksheetTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Wilayah</th>
                            <th>Cabang</th>
                            <th>Outlet</th>
                            <th>Kategori</th>
                            <th>Premises</th>
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
                    <h5 class="modal-title" id="exampleModalLabel">Premises Detail</h5>
                    <button type="button" class="close dismis-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body border-0">
                    <div class="row">
                        <div class="col-lg-4 col-sm-12 text-left">
                            <div class="mb-3">
                                <label for="wilayah" class="form-label">Wilayah</label>
                                <input type="text" class="form-control" id="wilayah" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="cabang" class="form-label">Cabang</label>
                                <input type="text" class="form-control" id="cabang" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="outlet" class="form-label">Outlet</label>
                                <input type="text" class="form-control" id="outlet" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="kondisi" class="form-label">Penilaian Outlet</label>
                                <input type="text" class="form-control" id="kondisi" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="created_at" class="form-label">Tanggal Outlet Input</label>
                                <input type="text" class="form-control" id="created_at" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 text-left">
                            <div class="mb-3">
                                <label for="nama_pusat" class="form-label">Nama SQ</label>
                                <input type="text" class="form-control" id="nama_pusat" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="nama_smw" class="form-label">Nama SMW</label>
                                <input type="text" class="form-control" id="nama_smw" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="kondisi_smw" class="form-label">Penilaian SMW</label>
                                <input type="text" class="form-control" id="kondisi_smw" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="catatan" class="form-label">Catatan Outlet</label>
                                <textarea name="catatan" id="catatan" class="form-control" readonly></textarea>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <div class="mb-3">
                                <label for="catatan_smw" class="form-label">Catatan SMW</label>
                                <textarea name="catatan" id="catatan_smw" class="form-control" readonly></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="catatan_pusat" class="form-label">Catatan SQ</label>
                                <textarea name="catatan" id="catatan_pusat" class="form-control" readonly></textarea>
                            </div>
                            <p>
                                <button class="btn btn-primary" type="button" data-toggle="collapse"
                                    data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                    Download File Pendukung
                                </button>
                            </p>
                            <div class="collapse border rounded" id="collapseExample">
                                <div class="card card-body d-flex" id="download-list">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary dismis-modal"
                        data-dismiss="evidenceModal">Sembunyikan</button>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="/assets/css/datatable.css">
@endsection

@section('script')
    <script type="text/javascript">
        $('.dismis-modal').click(function(e) {
            e.preventDefault();
            $("#evidenceModal").modal('hide');
        });

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
                    url: '/load_report',
                    data: function(d) {
                        d.startDate = $('#startDate').val();
                        d.endDate = $('#endDate').val();
                        d.wilayah = $('#filterWilayah').val();
                        d.parameter_premises = $('#parameter_premises').val();
                        d.parameter_kondisi = $('#parameter_kondisi').val();
                        d.parameter_outlet = $('#parameter_outlet').val();
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
                        data: 'kategori'
                    },
                    {
                        data: 'premises'
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

            $('#startDate, #endDate, #filterWilayah').change(function(e) {
                e.preventDefault();
                table.draw();
            });
        });

        const form = ['wilayah', 'cabang', 'outlet', 'catatan', 'kondisi', 'created_at', 'nama_pusat',
            'nama_smw', 'kondisi_smw', 'catatan_outlet', 'catatan_smw', 'catatan_pusat'
        ];

        $(document).on('click', '.btn-detail', function(param) {
            $('#evidenceModal').modal('show');

            var id = $(this).attr('data-id');

            $.ajax({
                type: "POST",
                url: "/get_detail",
                data: {
                    id: id
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var data = response.data;
                    var evidence = response.evidence;
                    console.log(data, evidence);

                    form.forEach((element, index) => {
                        $(`#${element}`).val(data[form[index]]);
                    });

                    //---- Set Download List
                    let html = '';
                    if (data['berita_acara']) {
                        html +=
                            `<a href="https://elvis-premises.online/assets/berita_acara/${data.berita_acara}" class="btn btn-outline-primary mb-2" download="${data.berita_acara}">Download Berita Acara</a>`;
                    }

                    if (evidence.length > 0) {
                        evidence.forEach((element, index) => {
                            html +=
                                `<a href="https://elvis-premises.online/assets/evidence/${element.file_name}" class="btn btn-outline-primary mb-2" download="${element.file_name}">Evidence #${index}</a>`;
                        });
                    }

                    $("#download-list").html(html);
                }
            });
        })
    </script>
@endsection
