<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Failed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8 text-center">
        <div class="card">
          <div class="card-header bg-white text-danger">
            <h3>Payment Failed</h3>
          </div>
          <div class="card-body">
            <p class="lead">
                Maaf, pembayaran Anda gagal diproses. Silakan coba lagi.
            </p>
            <p>Transaction ID: <strong>{{ $invoice }}</strong></p>
            <a href="/" class="btn btn-secondary mt-3">Go to Homepage</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
