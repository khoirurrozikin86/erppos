@extends('layouts.admin')

@section('content')
    <div class="page-content">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h4 class="mb-1">
                    Email Settings
                </h4>

                <p class="text-muted mb-0">
                    Konfigurasi email untuk sistem ERP.
                </p>
            </div>

        </div>


        <div class="row">

            <div class="col-lg-8">

                <div class="card">

                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            SMTP Configuration
                        </h6>
                    </div>


                    <form id="emailSettingForm">

                        @csrf

                        <div class="card-body">

                            {{-- Mailer --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Mailer
                                </label>

                                <select name="mail_mailer" class="form-select">
                                    <option value="smtp" {{ $setting->mail_mailer === 'smtp' ? 'selected' : '' }}>
                                        SMTP
                                    </option>
                                </select>

                            </div>


                            {{-- Host --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    SMTP Host
                                </label>

                                <input type="text" name="mail_host" class="form-control"
                                    value="{{ $setting->mail_host }}" placeholder="smtp.example.com">

                            </div>


                            <div class="row">

                                {{-- Port --}}
                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Port
                                    </label>

                                    <input type="number" name="mail_port" class="form-control"
                                        value="{{ $setting->mail_port }}">

                                </div>


                                {{-- Encryption --}}
                                <div class="col-md-8 mb-3">

                                    <label class="form-label">
                                        Encryption
                                    </label>

                                    <select name="mail_encryption" class="form-select">

                                        <option value="">
                                            None
                                        </option>

                                        <option value="tls" {{ $setting->mail_encryption === 'tls' ? 'selected' : '' }}>
                                            TLS
                                        </option>

                                        <option value="ssl" {{ $setting->mail_encryption === 'ssl' ? 'selected' : '' }}>
                                            SSL
                                        </option>

                                    </select>

                                </div>

                            </div>


                            {{-- Username --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Username
                                </label>

                                <input type="text" name="mail_username" class="form-control"
                                    value="{{ $setting->mail_username }}">

                            </div>


                            {{-- Password --}}
                            <div class="mb-4">

                                <label class="form-label">
                                    Password
                                </label>

                                <input type="password" name="mail_password" class="form-control"
                                    placeholder="Kosongkan jika tidak ingin mengubah">

                            </div>


                            <hr>


                            <h6 class="mb-3">
                                Sender Information
                            </h6>


                            {{-- From Name --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    From Name
                                </label>

                                <input type="text" name="from_name" class="form-control"
                                    value="{{ $setting->from_name }}" placeholder="ERP Company">

                            </div>


                            {{-- From Email --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    From Email
                                </label>

                                <input type="email" name="from_email" class="form-control"
                                    value="{{ $setting->from_email }}" placeholder="noreply@example.com">

                            </div>


                            {{-- Active --}}
                            <div class="form-check form-switch">

                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ $setting->is_active ? 'checked' : '' }}>

                                <label class="form-check-label" for="is_active">
                                    Enable Email Service
                                </label>

                            </div>

                        </div>


                        <div class="card-footer d-flex justify-content-end">

                            <button type="submit" class="btn btn-primary" id="btnSaveEmail">
                                <i data-feather="save" class="me-1"></i>
                                Save Settings
                            </button>

                        </div>

                    </form>

                </div>

            </div>


            {{-- Test Email --}}
            <div class="col-lg-4">

                <div class="card">

                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            Test Email
                        </h6>
                    </div>

                    <div class="card-body">

                        <p class="text-muted">
                            Kirim email percobaan untuk memastikan
                            konfigurasi SMTP sudah benar.
                        </p>

                        <input type="email" id="test_email" class="form-control mb-3" placeholder="email@example.com">

                        <button type="button" class="btn btn-outline-primary w-100" id="btnTestEmail">
                            <i data-feather="send" class="me-1"></i>
                            Send Test Email
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection


@push('scripts')
    <script>
        $(function() {

            $('#emailSettingForm').on('submit', function(e) {

                e.preventDefault();

                const button = $('#btnSaveEmail');

                const data = $(this).serializeArray();

                data.push({
                    name: '_method',
                    value: 'PUT'
                });

                if (!$('#is_active').is(':checked')) {
                    data.push({
                        name: 'is_active',
                        value: '0'
                    });
                }

                button
                    .prop('disabled', true)
                    .html('Saving...');

                $.ajax({
                    url: '{{ route('super.settings.email.update') }}',
                    method: 'POST',
                    data: data,

                    success: function(response) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1800,
                            showConfirmButton: false
                        });

                    },

                    error: function(xhr) {

                        let message = 'Terjadi kesalahan.';

                        if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: message
                        });

                    },

                    complete: function() {

                        button
                            .prop('disabled', false)
                            .html(`
                        <i data-feather="save" class="me-1"></i>
                        Save Settings
                    `);

                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    }
                });

            });


            $('#btnTestEmail').on('click', function() {

                const email = $('#test_email').val();

                if (!email) {

                    Swal.fire({
                        icon: 'warning',
                        title: 'Email belum diisi',
                        text: 'Masukkan alamat email tujuan.'
                    });

                    return;
                }

                const button = $(this);

                button
                    .prop('disabled', true)
                    .html('Sending...');

                $.ajax({
                    url: '{{ route('super.settings.email.test') }}',
                    method: 'POST',

                    data: {
                        _token: '{{ csrf_token() }}',
                        email: email
                    },

                    success: function(response) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });

                    },

                    error: function(xhr) {

                        Swal.fire({
                            icon: 'error',
                            title: 'Email gagal dikirim',
                            text: xhr.responseJSON?.message ??
                                'Periksa kembali konfigurasi SMTP.'
                        });

                    },

                    complete: function() {

                        button
                            .prop('disabled', false)
                            .html(`
                        <i data-feather="send" class="me-1"></i>
                        Send Test Email
                    `);

                        if (typeof feather !== 'undefined') {
                            feather.replace();
                        }
                    }
                });

            });

        });
    </script>
@endpush
