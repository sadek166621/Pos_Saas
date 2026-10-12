<!DOCTYPE html>
<html lang="bn" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>POS কাউন্টার - {{ $branch->name ?? 'SmartPOS' }}</title>

  <!-- Color Modes (Forced Light Theme) -->
  <script src="{{ asset('assets/js/color-modes.js') }}"></script>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com/">
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/demo1/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/pos-saas-theme.css') }}">
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

  <style>
    body {
      font-family: 'Hind Siliguri', 'Inter', sans-serif;
      background-color: #f1f5f9;
      height: 100vh;
      overflow: hidden;
    }
    .pos-header {
      background: #0f172a;
      color: white;
      height: 60px;
      padding: 0 20px;
    }
    .pos-container {
      height: calc(100vh - 60px);
    }
    .product-grid-container {
      height: calc(100vh - 180px);
      overflow-y: auto;
    }
    .cart-items-container {
      height: calc(100vh - 380px);
      overflow-y: auto;
    }
    .pos-product-card {
      background: white;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 12px;
      cursor: pointer;
      transition: all 0.15s ease-in-out;
      user-select: none;
    }
    .pos-product-card:hover {
      border-color: #0d9488;
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.06);
    }
    .pos-product-card:active {
      transform: scale(0.98);
    }
    .category-pill {
      cursor: pointer;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      white-space: nowrap;
      background: #e2e8f0;
      color: #334155;
    }
    .category-pill.active {
      background: #0d9488;
      color: white;
    }
    .cart-table th { font-size: 12px; text-transform: uppercase; color: #64748b; }
    .cart-table td { vertical-align: middle; }
    .qty-btn { width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; }
    .total-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; }
    .pay-btn-big { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-size: 1.25rem; font-weight: bold; border-radius: 12px; }
    .pay-btn-big:hover { background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; }
    .network-badge { font-size: 11px; padding: 3px 8px; border-radius: 12px; font-weight: bold; }
  </style>
</head>
<body>

  <!-- TOP BAR -->
  <header class="pos-header d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1">
        <i data-lucide="arrow-left" class="icon-sm"></i> ড্যাশবোর্ড
      </a>
      <div class="d-flex align-items-center gap-2">
        <span class="fs-5 fw-bold text-white">Smart<span class="text-teal" style="color: #14b8a6;">POS</span></span>
        <span class="badge bg-primary text-white">{{ $branch->name }}</span>
      </div>
    </div>

    <!-- Shift & Network Status -->
    <div class="d-flex align-items-center gap-3">
      <span id="networkStatus" class="network-badge bg-success text-white">● Online</span>

      @if($activeShift)
        <div class="bg-dark bg-opacity-50 px-3 py-1 rounded border border-secondary text-light small d-none d-md-block">
          ক্যাশিয়ার: <strong>{{ auth()->user()->name }}</strong> | শুরু ক্যাশ: ৳{{ number_format($activeShift->opening_cash) }}
        </div>
        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#closeShiftModal">
          শিফট সমাপ্ত করুন
        </button>
      @else
        <button class="btn btn-sm btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#openShiftModal">
          ⚠️ ক্যাশ শিফট ওপেন করুন
        </button>
      @endif
    </div>
  </header>

  <!-- POS MAIN CONTAINER -->
  <div class="container-fluid pos-container p-3">
    <div class="row g-3 h-100">

      <!-- LEFT: PRODUCT CATALOG & BARCODE SCANNER (7 COLUMNS) -->
      <div class="col-lg-7 d-flex flex-column h-100">
        <!-- Search & Filter Controls -->
        <div class="bg-white p-3 rounded-4 border shadow-sm mb-3">
          <div class="row g-2 mb-2">
            <div class="col-md-8">
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                  <i data-lucide="scan" class="text-primary icon-sm"></i>
                </span>
                <input type="text" id="barcodeScannerInput" class="form-control border-start-0 ps-0" placeholder="বারকোড স্ক্যান করুন বা প্রোডাক্টের নাম দিয়ে খুঁজুন... (F2)" autofocus>
              </div>
            </div>
            <div class="col-md-4">
              <button class="btn btn-outline-secondary w-100" onclick="document.getElementById('barcodeScannerInput').value=''; filterProducts('');">
                রিসেট ফিল্টার
              </button>
            </div>
          </div>

          <!-- Category Pills -->
          <div class="d-flex gap-2 overflow-auto pb-1" id="categoryPillList">
            <span class="category-pill active" onclick="selectCategory(null, this)">সব ক্যাটাগরি</span>
            @foreach($categories as $cat)
              <span class="category-pill" onclick="selectCategory({{ $cat->id }}, this)">{{ $cat->name }}</span>
            @endforeach
          </div>
        </div>

        <!-- Product Cards Grid -->
        <div class="product-grid-container flex-grow-1 pe-1">
          <div class="row g-3" id="productGrid">
            @foreach($products as $prod)
              <div class="col-xl-3 col-lg-4 col-sm-6 product-item" data-id="{{ $prod['id'] }}" data-cat="{{ $prod['category_id'] }}" data-name="{{ strtolower($prod['name']) }}" data-barcode="{{ $prod['barcode'] }}" data-sku="{{ strtolower($prod['sku']) }}" onclick="addToCart({{ json_encode($prod) }})">
                <div class="pos-product-card h-100 d-flex flex-column justify-content-between">
                  <div>
                    <div class="d-flex justify-content-between align-items-start mb-1">
                      <span class="badge bg-light text-dark border font-monospace small" style="font-size: 10px;">{{ $prod['barcode'] ?? $prod['sku'] }}</span>
                      <span class="badge {{ $prod['stock'] > 5 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} small" style="font-size: 11px;">
                        স্টক: {{ (float)$prod['stock'] }}
                      </span>
                    </div>
                    <h6 class="fw-bold mb-2 text-dark text-truncate-2" style="font-size: 13px; line-height: 1.3;">{{ $prod['name'] }}</h6>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                    <span class="fw-bold text-primary fs-6">৳{{ number_format($prod['price'], 2) }}</span>
                    <button class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 12px;">+ কার্ট</button>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <!-- RIGHT: CART, CUSTOMER & CHECKOUT (5 COLUMNS) -->
      <div class="col-lg-5 d-flex flex-column h-100">
        <div class="bg-white rounded-4 border shadow-sm p-3 d-flex flex-column h-100">

          <!-- Customer Selection -->
          <div class="d-flex gap-2 mb-3">
            <select id="customerSelect" class="form-select form-select-sm fw-bold">
              @foreach($customers as $c)
                <option value="{{ $c->id }}" data-due="{{ $c->current_due }}">
                  {{ $c->name }} (বাকি: ৳{{ number_format($c->current_due) }})
                </option>
              @endforeach
            </select>
            <button class="btn btn-sm btn-outline-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#newCustomerModal">
              + নতুন কাস্টমার
            </button>
          </div>

          <!-- Cart Action Bar -->
          <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom">
            <span class="fw-bold text-secondary small">নির্বাচিত পণ্যসমূহ (<span id="cartCount">0</span>)</span>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-warning py-0 px-2 small" onclick="holdCart()">হোল্ড কার্ট</button>
              <button class="btn btn-sm btn-outline-danger py-0 px-2 small" onclick="clearCart()">খালি করুন</button>
            </div>
          </div>

          <!-- Cart Items Table -->
          <div class="cart-items-container flex-grow-1">
            <table class="table cart-table align-middle mb-0">
              <thead>
                <tr>
                  <th>পণ্য</th>
                  <th style="width: 100px;">পরিমাণ</th>
                  <th class="text-end" style="width: 90px;">মূল্য</th>
                  <th style="width: 30px;"></th>
                </tr>
              </thead>
              <tbody id="cartTableBody">
                <tr>
                  <td colspan="4" class="text-center text-muted py-5">
                    কার্টে কোনো পণ্য নেই। বাম পাশ থেকে পণ্য ক্লিক করুন বা বারকোড স্ক্যান করুন।
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Cart Summary & Calculation -->
          <div class="total-box p-3 mt-3">
            <div class="d-flex justify-content-between small text-secondary mb-1">
              <span>সাবটোটাল:</span>
              <span class="fw-bold text-dark" id="subtotalDisplay">৳০.০০</span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small text-secondary">ছাড় / ডিসকাউন্ট (৳):</span>
              <input type="number" id="discountInput" class="form-control form-control-sm text-end" style="width: 90px;" value="0" min="0" oninput="calculateTotals()">
            </div>

            <div class="d-flex justify-content-between small text-secondary mb-2">
              <span>ভ্যাট (VAT):</span>
              <span class="fw-bold text-dark" id="vatDisplay">৳০.০০</span>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top pt-2">
              <span class="fs-5 fw-bold text-dark">সর্বমোট বিল:</span>
              <span class="fs-3 fw-bold text-primary" id="grandTotalDisplay">৳০.০০</span>
            </div>
          </div>

          <!-- Checkout Button -->
          <button class="btn pay-btn-big w-100 py-3 mt-3 shadow-sm d-flex justify-content-center align-items-center gap-2" onclick="openPaymentModal()">
            <i data-lucide="check-circle" class="icon-md"></i>
            বিল পরিশোধ / চেকআউট (F4)
          </button>

        </div>
      </div>

    </div>
  </div>

  <!-- PAYMENT & CHECKOUT MODAL (SPLIT PAYMENT) -->
  <div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow-lg">
        <div class="modal-header bg-dark text-white rounded-top-4">
          <h5 class="modal-title fw-bold">পেমেন্ট গ্রহণ ও ইনভয়েস তৈরি</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="bg-light p-3 rounded-3 text-center mb-3 border">
            <div class="small text-secondary mb-1">সর্বমোট প্রদেয় টাকা</div>
            <h2 class="fw-bold text-primary mb-0" id="modalPayTotal">৳০.০০</h2>
          </div>

          <!-- Split Payment Inputs -->
          <div class="mb-3">
            <label class="form-label small fw-bold">নগদ প্রদান (Cash):</label>
            <input type="number" id="payCash" class="form-control fw-bold" value="0" oninput="updatePaymentMath()">
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">বিকাশ (bKash):</label>
              <input type="number" id="payBkash" class="form-control" value="0" oninput="updatePaymentMath()">
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">নগদ / রকেট / কার্ড:</label>
              <input type="number" id="payCard" class="form-control" value="0" oninput="updatePaymentMath()">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold">Trx ID / রেফারেন্স (ঐচ্ছিক):</label>
            <input type="text" id="payTrxId" class="form-control" placeholder="Trx ID (যদি থাকে)">
          </div>

          <!-- Due & Change Calculation -->
          <div class="p-3 bg-light rounded-3 border">
            <div class="d-flex justify-content-between small mb-1">
              <span>মোট গ্রহণ করা হয়েছে:</span>
              <strong id="modalTotalReceived">৳০.০০</strong>
            </div>
            <div class="d-flex justify-content-between small mb-1">
              <span>ফেরত দিতে হবে (Change):</span>
              <strong class="text-success" id="modalChange">৳০.০০</strong>
            </div>
            <div class="d-flex justify-content-between small">
              <span>অবশিষ্ট বকেয়া (Due):</span>
              <strong class="text-danger" id="modalDue">৳০.০০</strong>
            </div>
          </div>
        </div>
        <div class="modal-footer p-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
          <button type="button" class="btn btn-success fw-bold px-4" id="submitSaleBtn" onclick="submitSale()">
            অর্ডার নিশ্চিত ও প্রিন্ট করুন ✓
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- OPEN SHIFT MODAL -->
  <div class="modal fade" id="openShiftModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header bg-warning rounded-top-4">
          <h5 class="modal-title fw-bold text-dark">ক্যাশ শিফট চালু করুন</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form onsubmit="submitOpenShift(event)">
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold">শুরুর ক্যাশ ফ্লোট (Opening Cash ৳):</label>
              <input type="number" id="openingCashInput" class="form-control form-control-lg fw-bold" required value="5000" min="0">
              <small class="text-muted">ক্যাশ ড্রয়ারে যে টাকা দিয়ে শিফট শুরু হচ্ছে</small>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold">নোট (ঐচ্ছিক):</label>
              <textarea id="openingShiftNotes" class="form-control" rows="2" placeholder="সকালের শিফট শুরু..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning fw-bold px-4">শিফট শুরু করুন</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- CLOSE SHIFT MODAL -->
  <div class="modal fade" id="closeShiftModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header bg-danger text-white rounded-top-4">
          <h5 class="modal-title fw-bold">ক্যাশ শিফট সমাপ্ত ও ক্যাশ মেলানো</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form onsubmit="submitCloseShift(event)">
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold">ড্রয়ারে থাকা প্রকৃত ক্যাশ (Actual Cash ৳):</label>
              <input type="number" id="actualCashInput" class="form-control form-control-lg fw-bold" required min="0" placeholder="টাকা গণনা করে লিখুন">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold">নোট / কারণ:</label>
              <textarea id="closingShiftNotes" class="form-control" rows="2" placeholder="শিফট সমাপ্তির মন্তব্য..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
            <button type="submit" class="btn btn-danger fw-bold px-4">শিফট বন্ধ করুন</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- NEW CUSTOMER QUICK MODAL -->
  <div class="modal fade" id="newCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header bg-primary text-white rounded-top-4">
          <h5 class="modal-title fw-bold">নতুন কাস্টমার যোগ</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('customers.store') }}" method="POST">
          @csrf
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold">কাস্টমারের নাম</label>
              <input type="text" name="name" class="form-control" required placeholder="উদাঃ মোঃ তানভীর">
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold">মোবাইল নম্বর</label>
              <input type="text" name="phone" class="form-control" placeholder="০১৭১১XXXXXX">
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label small fw-bold">কাস্টমার টাইপ</label>
                <select name="type" class="form-select">
                  <option value="regular">Regular</option>
                  <option value="vip">VIP</option>
                  <option value="wholesale">Wholesale</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label small fw-bold">শুরু বকেয়া (Due ৳)</label>
                <input type="number" name="previous_due" class="form-control" value="0">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary fw-bold">সংরক্ষণ করুন</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
  <script src="{{ asset('assets/js/app.js') }}"></script>

  <!-- POS CORE JAVASCRIPT -->
  <script>
    let cart = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function addToCart(product) {
      const existing = cart.find(item => item.product_id === product.id);
      if (existing) {
        existing.quantity += 1;
      } else {
        cart.push({
          product_id: product.id,
          name: product.name,
          unit_price: parseFloat(product.price),
          quantity: 1,
          vat_percent: parseFloat(product.vat_percent || 0),
          discount: 0
        });
      }
      renderCart();
    }

    function updateQty(index, change) {
      cart[index].quantity += change;
      if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
      }
      renderCart();
    }

    function removeFromCart(index) {
      cart.splice(index, 1);
      renderCart();
    }

    function clearCart() {
      if (cart.length > 0 && confirm('আপনি কি নিশ্চিত যে কার্ট খালি করবেন?')) {
        cart = [];
        renderCart();
      }
    }

    function holdCart() {
      if (cart.length === 0) return alert('কার্ট খালি!');
      localStorage.setItem('pos_held_cart_' + Date.now(), JSON.stringify(cart));
      cart = [];
      renderCart();
      alert('কার্ট সফলভাবে হোল্ড করা হয়েছে!');
    }

    function renderCart() {
      const tbody = document.getElementById('cartTableBody');
      const countEl = document.getElementById('cartCount');

      if (cart.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-5">কার্টে কোনো পণ্য নেই। বাম পাশ থেকে পণ্য ক্লিক করুন বা বারকোড স্ক্যান করুন।</td></tr>';
        countEl.innerText = '0';
        calculateTotals();
        return;
      }

      countEl.innerText = cart.length;
      let html = '';

      cart.forEach((item, idx) => {
        const itemTotal = (item.quantity * item.unit_price) - item.discount;
        html += `
          <tr>
            <td>
              <div class="fw-bold text-dark text-truncate" style="max-width: 160px;">${item.name}</div>
              <small class="text-muted">৳${item.unit_price.toFixed(2)}</small>
            </td>
            <td>
              <div class="d-flex align-items-center gap-1">
                <button class="btn btn-sm btn-outline-secondary qty-btn" onclick="updateQty(${idx}, -1)">-</button>
                <input type="text" class="form-control form-control-sm text-center px-1" style="width: 36px;" value="${item.quantity}" readonly>
                <button class="btn btn-sm btn-outline-secondary qty-btn" onclick="updateQty(${idx}, 1)">+</button>
              </div>
            </td>
            <td class="text-end fw-bold text-dark">৳${itemTotal.toFixed(2)}</td>
            <td class="text-end">
              <button class="btn btn-sm text-danger p-0 border-0" onclick="removeFromCart(${idx})">×</button>
            </td>
          </tr>
        `;
      });

      tbody.innerHTML = html;
      calculateTotals();
    }

    function calculateTotals() {
      let subtotal = 0;
      let totalVat = 0;

      cart.forEach(item => {
        const line = item.quantity * item.unit_price;
        subtotal += line;
        if (item.vat_percent > 0) {
          totalVat += (line * item.vat_percent / 100);
        }
      });

      const discount = parseFloat(document.getElementById('discountInput').value || 0);
      const grandTotal = Math.max(0, subtotal - discount + totalVat);

      document.getElementById('subtotalDisplay').innerText = '৳' + subtotal.toFixed(2);
      document.getElementById('vatDisplay').innerText = '৳' + totalVat.toFixed(2);
      document.getElementById('grandTotalDisplay').innerText = '৳' + grandTotal.toFixed(2);

      return { subtotal, discount, vat: totalVat, grandTotal };
    }

    function openPaymentModal() {
      if (cart.length === 0) {
        alert('অনুগ্রহ করে কার্টে পণ্য যোগ করুন!');
        return;
      }

      const totals = calculateTotals();
      document.getElementById('modalPayTotal').innerText = '৳' + totals.grandTotal.toFixed(2);
      document.getElementById('payCash').value = totals.grandTotal.toFixed(2);
      document.getElementById('payBkash').value = '0';
      document.getElementById('payCard').value = '0';

      updatePaymentMath();
      new bootstrap.Modal(document.getElementById('paymentModal')).show();
    }

    function updatePaymentMath() {
      const totals = calculateTotals();
      const cash = parseFloat(document.getElementById('payCash').value || 0);
      const bkash = parseFloat(document.getElementById('payBkash').value || 0);
      const card = parseFloat(document.getElementById('payCard').value || 0);

      const totalReceived = cash + bkash + card;
      const change = Math.max(0, totalReceived - totals.grandTotal);
      const due = Math.max(0, totals.grandTotal - totalReceived);

      document.getElementById('modalTotalReceived').innerText = '৳' + totalReceived.toFixed(2);
      document.getElementById('modalChange').innerText = '৳' + change.toFixed(2);
      document.getElementById('modalDue').innerText = '৳' + due.toFixed(2);
    }

    function submitSale() {
      const totals = calculateTotals();
      const customerId = document.getElementById('customerSelect').value;

      const cash = parseFloat(document.getElementById('payCash').value || 0);
      const bkash = parseFloat(document.getElementById('payBkash').value || 0);
      const card = parseFloat(document.getElementById('payCard').value || 0);
      const trxId = document.getElementById('payTrxId').value;

      let payments = [];
      if (cash > 0) payments.push({ method: 'cash', amount: cash });
      if (bkash > 0) payments.push({ method: 'bkash', amount: bkash, trx_id: trxId });
      if (card > 0) payments.push({ method: 'card', amount: card, trx_id: trxId });

      const totalPaid = Math.min(totals.grandTotal, cash + bkash + card);
      const paymentMethod = payments.length > 1 ? 'split' : (payments[0]?.method || 'cash');

      const payload = {
        customer_id: customerId,
        items: cart,
        discount_amount: totals.discount,
        vat_amount: totals.vat,
        paid_amount: totalPaid,
        payment_method: paymentMethod,
        payments: payments,
      };

      const submitBtn = document.getElementById('submitSaleBtn');
      submitBtn.disabled = true;
      submitBtn.innerText = 'প্রসেস হচ্ছে...';

      fetch("{{ route('pos.checkout') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
          "Accept": "application/json"
        },
        body: JSON.stringify(payload)
      })
      .then(r => r.json())
      .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'অর্ডার নিশ্চিত ও প্রিন্ট করুন ✓';

        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
          window.open(data.invoice_url, '_blank');
          cart = [];
          renderCart();
          alert(data.message);
        } else {
          alert('ত্রুটি: ' + data.message);
        }
      })
      .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'অর্ডার নিশ্চিত ও প্রিন্ট করুন ✓';
        alert('সার্ভার ত্রুটি: ' + err.message);
      });
    }

    // Barcode scanner & filter
    document.getElementById('barcodeScannerInput').addEventListener('keyup', function(e) {
      if (e.key === 'Enter') {
        const val = this.value.trim().toLowerCase();
        const found = @json($products).find(p => p.barcode === val || p.sku.toLowerCase() === val);
        if (found) {
          addToCart(found);
          this.value = '';
          return;
        }
      }
      filterProducts(this.value.trim().toLowerCase());
    });

    function filterProducts(query) {
      const items = document.querySelectorAll('.product-item');
      items.forEach(el => {
        const name = el.getAttribute('data-name');
        const barcode = el.getAttribute('data-barcode');
        const sku = el.getAttribute('data-sku');
        const match = !query || name.includes(query) || barcode.includes(query) || sku.includes(query);
        el.style.display = match ? 'block' : 'none';
      });
    }

    function selectCategory(catId, pillEl) {
      document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
      pillEl.classList.add('active');

      const items = document.querySelectorAll('.product-item');
      items.forEach(el => {
        const itemCat = el.getAttribute('data-cat');
        const match = !catId || itemCat == catId;
        el.style.display = match ? 'block' : 'none';
      });
    }

    // Shifts
    function submitOpenShift(e) {
      e.preventDefault();
      const cash = document.getElementById('openingCashInput').value;
      const notes = document.getElementById('openingShiftNotes').value;

      fetch("{{ route('pos.open-shift') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken, "Accept": "application/json" },
        body: JSON.stringify({ opening_cash: cash, notes: notes })
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          alert(d.message);
          window.location.reload();
        } else {
          alert(d.message);
        }
      });
    }

    function submitCloseShift(e) {
      e.preventDefault();
      const cash = document.getElementById('actualCashInput').value;
      const notes = document.getElementById('closingShiftNotes').value;

      fetch("{{ route('pos.close-shift') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": csrfToken, "Accept": "application/json" },
        body: JSON.stringify({ actual_cash: cash, notes: notes })
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          alert(`${d.message}\nপ্রত্যাশিত ক্যাশ: ৳${d.expected_cash}\nপ্রকৃত ক্যাশ: ৳${d.actual_cash}\nপার্থক্য: ৳${d.difference}`);
          window.location.reload();
        } else {
          alert(d.message);
        }
      });
    }

    // Keyboard Shortcuts (F2: Scanner, F4: Checkout)
    window.addEventListener('keydown', function(e) {
      if (e.key === 'F2') {
        e.preventDefault();
        document.getElementById('barcodeScannerInput').focus();
      } else if (e.key === 'F4') {
        e.preventDefault();
        openPaymentModal();
      }
    });

    // Offline status detection
    window.addEventListener('online', () => {
      document.getElementById('networkStatus').className = 'network-badge bg-success text-white';
      document.getElementById('networkStatus').innerText = '● Online';
    });
    window.addEventListener('offline', () => {
      document.getElementById('networkStatus').className = 'network-badge bg-danger text-white';
      document.getElementById('networkStatus').innerText = '● Offline';
    });
  </script>
</body>
</html>
