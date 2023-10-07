@extends('adminlte::page')

@section('title', 'Tambah Kegiatan')

@section('content_header')
    <h1>Tambah Kegiatan</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6">
                            <button id="save-data" class="btn btn-primary float-right">Simpan</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="description">Deskripsi Kegiatan</label>
                                <input type="text" id="description" class="form-control" placeholder="Deskripsi Kegiatan" name="description" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="budget">Budget</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input id="budget" type="text" class="form-control float-right" placeholder="Budget" name="budget" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Tanggal Kegiatan</label>
                                <input type="date" id="date" class="form-control" placeholder="Tanggal Kegiatan" name="date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document">Dokumen</label>
                                <input type="file" id="document" class="form-control" placeholder="Dokumen" name="document" required>
                            </div>
                        </div>
                    </div>
                    <form id="form-cart" action="#">
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="product">Pilih Produk</label>
                                    <select id="product" class="form-control select2" name="product_id" style="width: 100%;" required>
                                        <option value="">--Pilih Produk--</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                    data-min-price="{{ $product->min_price }}"
                                                    data-max-price="{{ $product->max_price }}"
                                                    data-product-name="{{ $product->name }}">
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="price">Harga</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input id="price" type="text" class="form-control float-right" placeholder="Harga" name="price" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="qty">Jumlah</label>
                                    <input type="number" id="qty" class="form-control" placeholder="Jumlah barang" name="qty" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="text-white">.</label>
                                <button type="submit" class="btn btn-primary btn-block">Tambah Data</button>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th>Kuantiti</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-table">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function(){
            let select2 = $('.select2').select2();
            select2.data('select2').$selection.css('height', '38px');

            $('#budget, #price').inputmask("currency", {
                radixPoint: ",",
                groupSeparator: ".",
                digits: 0,
                autoGroup: true,
                prefix: '', //Space after $, this will not truncate the first character.
                rightAlign: false,
            });
        })

        $('#product').change(function (){
            if ($(this).val() === '') {
                return
            }

            let minPrice = $(this).find(':selected').data('min-price')
            let maxPrice = $(this).find(':selected').data('max-price')

            $('#price').inputmask("currency", {
                radixPoint: ",",
                groupSeparator: ".",
                digits: 0,
                autoGroup: true,
                prefix: '', //Space after $, this will not truncate the first character.
                rightAlign: false,
                min: minPrice,
                max: maxPrice,
            });

            $('#price').focus()
        })

        let carts = [];
        $('#form-cart').submit(function (e) {
            e.preventDefault()

            let values = $(this).serializeArray()
            let form = {
                product_name: $('#product').find(':selected').data('product-name')
            };
            for (let value of values) {
                form[value.name] = value.value
            }

            carts.push(form)
            recalculateCart()

            $("#form-cart")[0].reset()
            $('#product').val('').trigger('change');
            $('#price').inputmask("currency", {
                radixPoint: ",",
                groupSeparator: ".",
                digits: 0,
                autoGroup: true,
                prefix: '', //Space after $, this will not truncate the first character.
                rightAlign: false,
                min: 0,
                max: 0,
            });
        })

        function deleteCart(i) {
            carts.splice(i, 1)
            recalculateCart()
        }

        function recalculateCart() {
            $('#cart-table').html('')

            let total = 0;
            carts.forEach((cart, i) => {
                let price = cart.price;
                let subtotal = Number(price.replace('.', '')) * cart.qty;
                let subtotalFormatted = subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                total += subtotal

                let row = `<tr><td>${cart.product_name}</td><td>${cart.price}</td><td>${cart.qty}</td><td>${subtotalFormatted}</td><td><button onclick="deleteCart(${i})" class="btn btn-sm btn-danger">Hapus</button></td></tr>`

                $('#cart-table').append(row)
            })

            if (carts.length > 0) {
                let totalFormatted = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                $('#cart-table').append(`<tr><td colspan="3">Total</td><td>${totalFormatted}</td><td></td></tr>`)
            }
        }

        $('#save-data').click(function() {
            let data = new FormData()
            if (typeof $('#document')[0].files[0] !== 'undefined') {
                data.append('document', $('#document')[0].files[0])
            }
            data.append('description', $('#description').val())
            data.append('budget', $('#budget').val())
            data.append('date', $('#date').val())
            carts.forEach((cart, i) => {
                data.append(`details[${i}][product_id]`, cart.product_id)
                data.append(`details[${i}][price]`, cart.price)
                data.append(`details[${i}][qty]`, cart.qty)
            })

            $.ajax({
                type: "POST",
                url: '{{ route('activities.store') }}',
                data: data,
                contentType: false,
                cache: false,
                processData:false,
            }).done(function(data) {

            })
            .fail(function(data) {
                console.log(data)
            })
            .always(function() {

            });
        })
    </script>
@stop
