@extends('adminlte::page')

@section('title', 'Edit Kegiatan')

@section('content_header')
    <h1>Edit Kegiatan</h1>
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
                                <input type="text" id="description" class="form-control" placeholder="Deskripsi Kegiatan" name="description" value="{{ $activity->description }}" required>
                                <div id="description-feedback" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="budget">Budget</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input id="budget" type="text" class="form-control float-right" placeholder="Budget" name="budget" value="{{ $activity->budget }}" required>
                                    <div id="budget-feedback" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Tanggal Kegiatan</label>
                                <input type="date" id="date" class="form-control" placeholder="Tanggal Kegiatan" name="date" value="{{ $activity->date }}" required>
                                <div id="date-feedback" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document">Dokumen</label>
                                <input type="file" id="document" class="form-control" placeholder="Dokumen" name="document" required>
                                <div id="document-feedback" class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered" id="cart-table">
                                <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Harga</th>
                                    <th>Kuantiti</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="3" class="text-bold">Total</td>
                                    <td id="total">0</td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <p class="text-center text-danger" id="details-feedback"></p>
                        </div>
                        <button type="button" class="btn btn-primary btn-block" id="btn-add-cart">Tambah Data</button>
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

        // keep select2 focus
        // on first focus (bubbles up to document), open the menu
        $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
            $(this).closest(".select2-container").siblings('select:enabled').select2('open');
        });

        // steal focus during close - only capture once and stop propogation
        $('select.select2').on('select2:closing', function (e) {
            $(e.target).data("select2").$selection.one('focus focusin', function (e) {
                e.stopPropagation();
            });
        });

        let products = @json($products);
        let budget = '{{ $activity->budget }}';
        budget = Number(budget.replace(/\./g, ''))
        let total = 0;

        $(document).ready(function(){
            $('#budget, #price').inputmask("currency", {
                radixPoint: ",",
                groupSeparator: ".",
                digits: 0,
                autoGroup: true,
                prefix: '', //Space after $, this will not truncate the first character.
                rightAlign: false,
            });
        })

        $('#budget').change(function () {
            budget = Number($(this).val().replace(/\./g, ''))
        })

        let i = 0
        let oldCarts = @json($carts);
        if (oldCarts.length > 0) {
            oldCarts.forEach((cart, t) => {
                let productOptions = `<option value="">--Pilih Produk--</option>`;
                products.forEach((product, i) => {
                    productOptions += `<option value="${product.id}" data-min-price="${product.min_price}" data-max-price="${product.max_price}">${product.name}</option>`
                })

                let subtotal = Number(cart.price.replace(/\./g, '')) * cart.qty;
                let subtotalFormatted = subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                let row = `<tr class=".cart-row">`+
                    `<td><select class="form-control select2 input-product" id="input-product-${i}" name="product[]" onChange="setInputPrice(this)">${productOptions}</select></td>`+
                    `<td><input type="text" class="form-control input-price" id="input-price-${i}" value="${cart.price}" onkeydown="setQty(event, this)" name="price[]"/></td>`+
                    `<td><input type="number" class="form-control input-qty" id="input-qty-${i}" value="${cart.qty}" onkeydown="setSubtotal(event, this)"  name="qty[]"/></td>`+
                    `<td class="subtotal" id="subtotal-${i}">${subtotalFormatted}</td>`+
                    `<td><button class="btn btn-sm btn-danger btn-delete-cart">Hapus</button></td>`+
                    `</tr>`;

                $('#cart-table > tbody:last-child').before(row)

                $(`#input-product-${i}`).val(cart.product_id)

                let product = products.find(product => product.id === cart.product_id);

                $(`#input-price-${i}`).inputmask("currency", {
                    radixPoint: ",",
                    groupSeparator: ".",
                    digits: 0,
                    autoGroup: true,
                    prefix: '', //Space after $, this will not truncate the first character.
                    rightAlign: false,
                    min: product.min_price,
                    max: product.max_price,
                });

                i++;
            })

            $('.select2').select2();
            $('.select2').each(function() {
                if ($(this).data('select2')) {
                    $(this).data('select2').$selection.css('height', '38px');
                }
            });

            calculateTotal()
        }


        $('#btn-add-cart').click(function () {
            if (budget === 0) {
                Swal.fire(
                    'Error!',
                    "input budget terlebih dahulu",
                    'error'
                )

                return
            }

            if (total > budget) {
                Swal.fire(
                    'Error!',
                    "total sudah melebihi budget",
                    'error'
                )

                return
            }

            let productOptions = `<option value="">--Pilih Produk--</option>`;
            products.forEach((product, i) => {
                productOptions += `<option value="${product.id}" data-min-price="${product.min_price}" data-max-price="${product.max_price}">${product.name}</option>`
            })

            let row = `<tr class=".cart-row">`+
                `<td><select class="form-control select2 input-product" id="input-product-${i}" name="product[]" onChange="setInputPrice(this)">${productOptions}</select></td>`+
                `<td><input type="text" class="form-control input-price" id="input-price-${i}" onkeydown="setQty(event, this)" name="price[]"/></td>`+
                `<td><input type="number" class="form-control input-qty" id="input-qty-${i}" onkeydown="setSubtotal(event, this)"  name="qty[]"/></td>`+
                `<td class="subtotal" id="subtotal-${i}"></td>`+
                `<td><button class="btn btn-sm btn-danger btn-delete-cart">Hapus</button></td>`+
                `</tr>`;

            $('#cart-table > tbody:last-child').before(row)

            $('.select2').select2();
            $('.select2').each(function() {
                if ($(this).data('select2')) {
                    $(this).data('select2').$selection.css('height', '38px');
                }
            });

            $(`#input-product-${i}`).focus();
            i++;
        })

        function setInputPrice(selector) {
            let productId = $(selector).val();
            let found = 0;
            $('#cart-table tr:gt(0)').each(function () {
                let inputProduct = $(this).find('.input-product')[0] || null;
                if (inputProduct !== null && inputProduct.value === productId) {
                    found++;
                }
            })

            if (found > 1) {
                $(selector).val('').trigger('change');

                Swal.fire(
                    'Error!',
                    "Produk duplikat",
                    'error'
                )

                return
            }

            let minPrice = $(selector).find(':selected').data('min-price')
            let maxPrice = $(selector).find(':selected').data('max-price')

            const row = selector.closest('tr');
            let inputPrice = row.querySelector('.input-price')
            let inputPriceId = inputPrice.getAttribute('id')

            $(`#${inputPriceId}`).inputmask("currency", {
                radixPoint: ",",
                groupSeparator: ".",
                digits: 0,
                autoGroup: true,
                prefix: '', //Space after $, this will not truncate the first character.
                rightAlign: false,
                min: minPrice,
                max: maxPrice,
            });

            window.setTimeout(function () {
                $(`#${inputPriceId}`).focus();
            }, 0);

            subtotal(row)
        }

        function setQty(event, selector) {
            if (event.key === "Enter") {
                const row = selector.closest('tr');
                let inputQty = row.querySelector('.input-qty');
                let inputQtyId = inputQty.getAttribute('id');

                $(`#${inputQtyId}`).focus();

                subtotal(row)
            }
        }

        function setSubtotal(event, selector) {
            if (event.key === "Enter") {
                event.preventDefault();
                const row = selector.closest('tr');
                subtotal(row)

                // do validation
                if (calculateTotal()) {
                    $('#btn-add-cart').trigger('click')
                } else {
                    Swal.fire(
                        'Error!',
                        "total sudah melebihi budget",
                        'error'
                    )
                }
            }
        }

        function subtotal(row) {
            let inputPrice = row.querySelector('.input-price');
            let inputQty = row.querySelector('.input-qty');
            let inputSubtotal = row.querySelector('.subtotal');
            let inputSubtotalId = inputSubtotal.getAttribute('id');

            let price = inputPrice.value;
            let qty = inputQty.value;

            let subtotal = Number(price.replace(/\./g, '')) * qty;
            let subtotalFormatted = subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            $(`#${inputSubtotalId}`).text(subtotalFormatted)
        }

        function calculateTotal() {
            total = 0
            $('#cart-table tr:gt(0)').each(function () {
                let inputPrice = $(this).find('.input-price')[0] || null;
                let inputQty = $(this).find('.input-qty')[0] || null;

                if (inputPrice !== null && inputQty !== null) {
                    let price = inputPrice.value;
                    let qty = inputQty.value;

                    let subtotal = Number(price.replace(/\./g, '')) * qty;
                    total += subtotal
                }
            });

            if (total > 0) {
                if (total > budget) {
                    return false
                }

                let totalFormatted = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                $('#total').text(totalFormatted)
            }

            return true
        }

        $('#cart-table').on('click', '.btn-delete-cart', function (){
            $(this).closest('tr').remove();

            calculateTotal();
        })

        $('#save-data').click(function() {
            // get row value add to carts
            let carts = [];
            $('#cart-table tr:gt(0)').each(function () {
                let inputProduct = $(this).find('.input-product')[0] || null;
                let inputPrice = $(this).find('.input-price')[0] || null;
                let inputQty = $(this).find('.input-qty')[0] || null;

                if (inputProduct !== null && inputPrice !== null && inputQty !== null) {
                    let productId = inputProduct.value;
                    let price = inputPrice.value;
                    let qty = inputQty.value;

                    if (productId !== '' && price !== '' && qty !== 0) {
                        carts.push({
                            product_id: productId,
                            price: price,
                            qty: qty,
                        })
                    }
                }
            });


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

            let link = '{{ route('activities.update', ['activity' => ':activity']) }}'
            link = link.replace(':activity', '{{ $activity->id }}')

            $.ajax({
                type: "POST",
                url: link,
                data: data,
                contentType: false,
                cache: false,
                processData:false,
            }).done(function(data) {
                window.location.href = '{{ route('activities.index') }}'
            }).fail(function(data) {
                if (data.status === 422) {
                    let errors = data['responseJSON']['errors'];
                    for (let error in errors) {
                        let field = error;
                        $(`#${field}`).addClass('is-invalid');
                        $(`#${field}-feedback`).text(errors[error][0]);
                    }
                }

                if (data.status === 400) {
                    let message = data['responseJSON']['message'];
                    Swal.fire(
                        'Error!',
                        message,
                        'error'
                    )
                }
            }).always(function() {

            });
        })
    </script>
@stop
