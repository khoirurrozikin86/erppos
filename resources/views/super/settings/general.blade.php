@extends('layouts.admin')

@section('content')
    <div class="page-content">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">General Settings</h4>
                <p class="text-muted mb-0">
                    Pengaturan umum sistem ERP.
                </p>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-8">

                <div class="card">

                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            System Configuration
                        </h6>
                    </div>

                    <form id="generalSettingForm">

                        @csrf

                        <div class="card-body">

                            {{-- Decimal Places --}}
                            <div class="mb-4">

                                <label class="form-label">
                                    Decimal Places
                                </label>

                                <input type="number" class="form-control" name="decimal_places" min="0"
                                    max="6" value="{{ $setting->decimal_places }}">

                                <small class="text-muted">
                                    Jumlah angka desimal yang digunakan sistem.
                                </small>

                            </div>


                            {{-- Negative Stock --}}
                            <div class="mb-4">

                                <label class="form-label d-block">
                                    Negative Stock
                                </label>

                                <div class="form-check form-switch">

                                    <input class="form-check-input" type="checkbox" name="negative_stock" value="1"
                                        id="negative_stock" {{ $setting->negative_stock ? 'checked' : '' }}>

                                    <label class="form-check-label" for="negative_stock">
                                        Allow Negative Stock
                                    </label>

                                </div>

                                <small class="text-muted">
                                    Mengizinkan stok menjadi negatif ketika transaksi.
                                </small>

                            </div>


                            {{-- Tax Included --}}
                            <div class="mb-4">

                                <label class="form-label d-block">
                                    Tax
                                </label>

                                <div class="form-check form-switch">

                                    <input class="form-check-input" type="checkbox" name="tax_included" value="1"
                                        id="tax_included" {{ $setting->tax_included ? 'checked' : '' }}>

                                    <label class="form-check-label" for="tax_included">
                                        Tax Included
                                    </label>

                                </div>

                                <small class="text-muted">
                                    Harga transaksi sudah termasuk pajak.
                                </small>

                            </div>

                        </div>


                        <div class="card-footer d-flex justify-content-end">

                            <button type="submit" class="btn btn-primary" id="btnSaveGeneralSetting">
                                <i data-feather="save" class="me-1"></i>
                                Save Settings
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {

            $('#generalSettingForm').on('submit', function(e) {

                e.preventDefault();

                const form = this;
                const button = $('#btnSaveGeneralSetting');

                const data = {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',

                    decimal_places: $('input[name="decimal_places"]').val(),

                    negative_stock: $('#negative_stock').is(':checked') ? 1 : 0,

                    tax_included: $('#tax_included').is(':checked') ? 1 : 0,
                };

                button
                    .prop('disabled', true)
                    .html('Saving...');

                $.ajax({
                    url: '{{ route('super.settings.general.update') }}',
                    type: 'POST',
                    data: data,

                    success: function(response) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1800,
                            showConfirmButton: false,
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
                            text: message,
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

        });
    </script>
@endpush
