@extends('app')

@section('content')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-sm-12 d-flex align-items-center justify-content-center">
                <div class="card p-5 rounded" style="width: 100%">
                    <img src="https://demo.satpamku.co.id/assets/images/logo.png" class="card-img-top" alt="logo"
                        style="height: 50px; object-fit: contain">
                    <div class="card-body mt-5">
                        <h5 class="card-title">Elvis Premises Dashboard</h5>
                        <p class="card-text">By Service Quality Division</p>
                        <form method="POST" action="/authorization" id="login-form">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" name="email"
                                    aria-describedby="emailHelp">
                                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                                    else.</small>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Password</label>
                                <input type="password" class="form-control" id="exampleInputPassword1" name="password">
                            </div>
                            <button type="submit"
                                class="btn btn-primary w-100 text-center d-flex justify-content-center">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function() {
            $('#login-form').submit(function(e) {
                e.preventDefault();

                var form = $(this);
                var actionUrl = form.attr('action');

                $.ajax({
                    type: "POST",
                    url: actionUrl,
                    data: form.serialize(),
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        Swal.showLoading();
                    },
                    success: function(response) {
                        Swal.fire(
                            'Good job!',
                            'Login Berhasil',
                            'success'
                        ).then(function() {
                            location.href = "/";
                        })
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong!'
                        })
                    }
                });
            });
        });
    </script>
@endsection
