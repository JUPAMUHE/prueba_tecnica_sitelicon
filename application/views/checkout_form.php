<!-- application/views/checkout_form.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Mi E-commerce</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Inicio</a>
                    </li>
                   
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <h1 class="mb-4">Pago Seguro</h1>
        <form action="<?php echo base_url('checkout/process_payment'); ?>" method="post" id="payment-form">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="product-description" class="form-label">Descripción del Producto</label>
                        <input type="text" id="product-description" name="product_description" class="form-control" value="Producto de Ejemplo" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Cantidad</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" value="" >
                    </div>
                    <div class="mb-3">
                        <label for="total_amount" class="form-label">Monto Total (USD)</label>
                        <input type="text" id="total_amount" name="total_amount" class="form-control" value="" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="card-element" class="form-label">Tarjeta de crédito o débito</label>
                        <div id="card-element" class="form-control"></div>
                        <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Pagar</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        var stripe = Stripe('<?php echo $stripe_publishable_key; ?>'); 
        var elements = stripe.elements();
        var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        var card = elements.create('card', {style: style});
        card.mount('#card-element');

        card.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    stripeTokenHandler(result.token);
                }
            });
        });

        var quantityInput = document.getElementById('quantity');
        var totalAmountInput = document.getElementById('total_amount');
        var pricePerUnit = 10; // Precio unitario en USD

        function updateTotalAmount() {
            var quantity = parseInt(quantityInput.value, 10);
            var totalAmount = quantity * pricePerUnit;
            totalAmountInput.value = totalAmount;
        }

        // Actualizar monto total cuando cambie la cantidad
        quantityInput.addEventListener('input', updateTotalAmount);

        function stripeTokenHandler(token) {
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);
            form.submit();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
