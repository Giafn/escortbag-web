<!-- Offcanvas container -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Keranjang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body bg-white pb-0 d-flex flex-column justify-content-between">
        <div> <!-- Add this style -->
            <div id="cart-items">
                <!-- Item cart template -->
            </div>
            <div id="cart-empty" class="row" style="display: none;">
                <div class="col-12 text-center">
                    <h5>Keranjang Kosong</h5>
                </div>
            </div>
        </div>
        <div class="sticky-bottom bg-white pt-3" style="width: 100%; margin-bottom: 0;">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Total</h5>
                <h5 id="cart-total">Rp 0</h5>
            </div>
            <a class="btn btn-dark w-100 mb-2" id="checkoutBtn" >Checkout</a>
        </div>
    </div>
</div>

<script>
    var formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0, // Mengatur minimum desimal ke 0
        maximumFractionDigits: 0 // Mengatur maksimum desimal ke 0
    });
    // Function to fetch cart data
    function fetchCartData() {
        $.ajax({
            url: '/cart', // Replace with your API endpoint
            method: 'GET',
            success: function(data) {
                updateCartItems(data.data);
                const cartTotal = data.data.reduce((acc, item) => acc + (item.total), 0);
                updateCartTotal(cartTotal);
                if (data.data.length == 0) {
                    $('#checkoutBtn').prop('disabled', true);
                } else {
                    $('#checkoutBtn').prop('disabled', false);
                    $('#checkoutBtn').attr('href', '/checkout/' + data.hash);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching cart data:', error);
            }
        });
    }

    // Function to update cart items in the UI
    function updateCartItems(items) {
        var cartItemsContainer = $('#cart-items');
        cartItemsContainer.empty(); // Clear existing items

        if (items.length > 0) {
            items.forEach(function(item) {
                var itemHtml = `
                    <div class="row item-cart transparent-75" data-id="${item.id}">
                        <div class="px-3 py-2">
                            <div class="row mb-2 align-items-center">
                                <div class="col-3">
                                    <img class="img-fluid" src="${item.item.image}" alt="">
                                </div>
                                <div class="col-9">
                                    <h5>${item.item.nama} - <small class="text-muted">${item.warna}</small></h5>
                                    <p>${formatter.format(item.total)}</p>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <input type="number" name="jumlah" class="form-control jumlah-input" value="${item.qty}" min="1" max="${item.item.stok > 10 ? 10 : item.item.stok}">
                                        </div>
                                        <button class="btn remove-item">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                cartItemsContainer.append(itemHtml);
            });
            $('#cart-empty').hide();
        } else {
            $('#cart-empty').show();
            $('#checkoutBtn').prop('disabled', true);
        }
    }

    // Function to update cart total in the UI
    function updateCartTotal(total) {
        total = formatter.format(total);
        $('#cart-total').text(total);
    }

    // Event listener for cart trigger
    $('#cart-trigger').click(function(event) {
        event.preventDefault();
        fetchCartData();
        $('#offcanvasExample').offcanvas('show');
    });

    // on change jumlah input
    $(document).on('change', '.jumlah-input', async function() {
        const itemId = $(this).closest('.item-cart').data('id');
        const qty = $(this).val();

        await updateQty(itemId, qty);
    });

    // on keyup jumlah input
    $(document).on('keyup', '.jumlah-input', async function() {
        const itemId = $(this).closest('.item-cart').data('id');
        const qty = $(this).val();
        if (qty < 1) {
            $(this).val(1);
        }
        if (qty > 10) {
            $(this).val(10);
        }
        await updateQty(itemId, qty);
    });

    async function updateQty(itemId, qty) {
        await $.ajax({
            url: '/cart/' + itemId, // Replace with your API endpoint
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                qty: qty
            },
            error: function(xhr, status, error) {
            }
        });

        fetchCartData();
    }

    // Event listener for removing item (dummy implementation)
    $(document).on('click', '.remove-item', async function() {
        await $.ajax({
            url: '/cart/' + $(this).closest('.item-cart').data('id'), // Replace with your API endpoint
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function(data) {
                toastr.success('Item removed from cart');
            },
            error: function(xhr, status, error) {
                console.error('Error removing item from cart:', error);
                toastr.error('Error removing item from cart');
            }
        });

        fetchCartData();
    });
</script>