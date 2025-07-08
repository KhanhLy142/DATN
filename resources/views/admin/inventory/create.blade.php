@extends('admin.layouts.master')

@section('title', 'Nhập hàng')

@section('content')
    <div class="container-fluid py-4">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Có lỗi xảy ra:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm rounded-4">
            <div class="card-header border-0 bg-transparent">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="fw-bold text-pink mb-0">
                            <i class="bi bi-box-arrow-in-down me-2"></i>Nhập hàng
                        </h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.inventory.store') }}" id="importForm">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-building me-1"></i>Nhà cung cấp
                                <span class="text-danger">*</span>
                            </label>
                            <select name="supplier_id" id="supplierSelect" class="form-select" required>
                                <option value="">-- Chọn nhà cung cấp --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-event me-1"></i>Ngày nhập
                            </label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                        </div>
                    </div>

                    <div id="availableBrands" class="mb-4" style="display: none;">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-primary mb-2">
                                    <i class="bi bi-award me-2"></i>Thương hiệu có sẵn từ nhà cung cấp này:
                                </h6>
                                <div id="brandsList" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0">
                                        <i class="bi bi-list-ul me-2"></i>Danh sách sản phẩm nhập
                                    </h5>
                                    <small class="text-muted">Chỉ hiển thị sản phẩm thuộc thương hiệu mà nhà cung cấp đã chọn cung cấp</small>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addRowBtn" disabled>
                                        <i class="bi bi-plus-circle me-1"></i>Thêm sản phẩm
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle import-table" id="itemsTable">
                                    <thead class="table-light text-center">
                                    <tr>
                                        <th>Sản phẩm <span class="text-danger">*</span></th>
                                        <th>Thương hiệu</th>
                                        <th>Số lượng <span class="text-danger">*</span></th>
                                        <th>Đơn giá nhập <span class="text-danger">*</span></th>
                                        <th>Thành tiền</th>
                                        <th>Thao tác</th>
                                    </tr>
                                    </thead>
                                    <tbody id="productRows">
                                    <tr class="item-row">
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-arrow-up me-2"></i>
                                            Vui lòng chọn nhà cung cấp trước để xem danh sách sản phẩm có thể nhập
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr class="table-info">
                                        <td colspan="4" class="text-end fw-bold">
                                            <i class="bi bi-calculator me-2"></i>Tổng cộng:
                                        </td>
                                        <td class="fw-bold text-end" id="grandTotal">0 ₫</td>
                                        <td></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-chat-left-text me-1"></i>Ghi chú
                                    </label>
                                    <textarea name="notes"
                                              class="form-control"
                                              rows="3"
                                              placeholder="Ghi chú thêm về phiếu nhập này (tùy chọn)...">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="text-end">
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="resetImportForm()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Làm mới
                                </button>
                                <button type="submit" class="btn btn-pink" id="submitBtn" disabled>
                                    <i class="bi bi-check-circle me-2"></i>Lưu phiếu nhập
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let availableProducts = [];
        let rowCounter = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const supplierSelect = document.getElementById('supplierSelect');
            const addRowBtn = document.getElementById('addRowBtn');
            const submitBtn = document.getElementById('submitBtn');

            supplierSelect.addEventListener('change', function() {
                const supplierId = this.value;

                if (supplierId) {
                    loadSupplierData(supplierId);
                } else {
                    resetProductList();
                }
            });

            addRowBtn.addEventListener('click', function() {
                addProductRow();
            });
        });

        async function loadSupplierData(supplierId) {
            try {
                showLoading('Đang tải dữ liệu nhà cung cấp...');

                const [brandsResponse, productsResponse] = await Promise.all([
                    fetch(`/admin/inventory/api/brands-by-supplier/${supplierId}`),
                    fetch(`/admin/inventory/api/products-by-supplier/${supplierId}`)
                ]);

                const brandsData = await brandsResponse.json();
                const productsData = await productsResponse.json();

                hideLoading();

                if (brandsData.success && productsData.success) {
                    availableProducts = productsData.products;
                    displayBrands(brandsData.brands);
                    enableProductSelection();

                    if (productsData.products.length === 0) {
                        showMessage('Nhà cung cấp này chưa có thương hiệu/sản phẩm nào. Vui lòng chọn nhà cung cấp khác.', 'warning');
                    }
                } else {
                    showMessage('Không thể tải dữ liệu nhà cung cấp', 'error');
                }
            } catch (error) {
                hideLoading();
                console.error('Error loading supplier data:', error);
                showMessage('Có lỗi xảy ra khi tải dữ liệu', 'error');
            }
        }

        function displayBrands(brands) {
            const brandsContainer = document.getElementById('brandsList');
            const availableBrandsDiv = document.getElementById('availableBrands');

            if (brands.length > 0) {
                brandsContainer.innerHTML = brands.map(brand =>
                    `<span class="badge bg-primary me-2 mb-1">${brand.name}${brand.country ? ` (${brand.country})` : ''}</span>`
                ).join('');
                availableBrandsDiv.style.display = 'block';
            } else {
                availableBrandsDiv.style.display = 'none';
            }
        }

        function enableProductSelection() {
            document.getElementById('addRowBtn').disabled = false;
            document.getElementById('submitBtn').disabled = false;

            const tbody = document.getElementById('productRows');
            tbody.innerHTML = '';
            addProductRow();
        }

        function resetProductList() {
            availableProducts = [];
            document.getElementById('availableBrands').style.display = 'none';
            document.getElementById('addRowBtn').disabled = true;
            document.getElementById('submitBtn').disabled = true;

            const tbody = document.getElementById('productRows');
            tbody.innerHTML = `
        <tr class="item-row">
            <td colspan="6" class="text-center text-muted py-4">
                <i class="bi bi-arrow-up me-2"></i>
                Vui lòng chọn nhà cung cấp trước để xem danh sách sản phẩm có thể nhập
            </td>
        </tr>
    `;

            updateGrandTotal();
        }

        function addProductRow() {
            const tbody = document.getElementById('productRows');
            const rowIndex = rowCounter++;

            const productOptions = availableProducts.map(product =>
                `<option value="${product.id}" data-brand="${product.brand_name}" data-price="${product.base_price}">
            ${product.display_name}
        </option>`
            ).join('');

            const row = document.createElement('tr');
            row.className = 'item-row';
            row.dataset.index = rowIndex;
            row.innerHTML = `
        <td>
            <select name="items[${rowIndex}][product_id]" class="form-select product-select" required onchange="handleProductChange(this)">
                <option value="">-- Chọn sản phẩm --</option>
                ${productOptions}
            </select>


            <div class="variant-selection mt-2" style="display: none;">
                <label class="form-label fw-semibold small">
                    <i class="bi bi-layers"></i> Chọn biến thể (tùy chọn)
                </label>
                <select class="form-select form-select-sm variant-select" name="items[${rowIndex}][variant_id]">
                    <option value="">-- Tất cả biến thể --</option>
                </select>
                <small class="text-muted">Để trống nếu muốn phân bổ đều cho tất cả biến thể</small>
            </div>
        </td>
        <td class="brand-cell text-center">-</td>
        <td>
            <input type="number" name="items[${rowIndex}][quantity]" class="form-control quantity-input text-center"
                   min="1" max="999999" placeholder="0" required onchange="calculateRowTotal(this)">
        </td>
        <td>
            <div class="input-group">
                <input type="number" name="items[${rowIndex}][unit_price]" class="form-control price-input text-end"
                       min="0" step="1000" placeholder="0" required onchange="calculateRowTotal(this)">
                <span class="input-group-text">₫</span>
            </div>
        </td>
        <td class="fw-bold text-end total-cell">0 ₫</td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm remove-row"
                    onclick="removeRow(this)" style="width: 35px; height: 35px;">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;

            tbody.appendChild(row);
            updateRemoveButtons();
        }

        async function handleProductChange(selectElement) {
            const productId = selectElement.value;
            const row = selectElement.closest('tr');
            const brandCell = row.querySelector('.brand-cell');
            const priceInput = row.querySelector('.price-input');
            const variantSelection = row.querySelector('.variant-selection');
            const variantSelect = row.querySelector('.variant-select');

            if (productId) {
                const selectedOption = selectElement.selectedOptions[0];
                const brandName = selectedOption.dataset.brand;
                const basePrice = selectedOption.dataset.price;

                brandCell.innerHTML = `<span class="badge bg-info">${brandName}</span>`;

                if (basePrice) {
                    priceInput.value = Math.round(basePrice * 0.7);
                }

                try {
                    const response = await fetch(`/admin/inventory/api/product-variants/${productId}`);
                    const data = await response.json();

                    if (data.length > 0) {
                        variantSelect.innerHTML = '<option value="">-- Tất cả biến thể --</option>' +
                            data.map(variant =>
                                `<option value="${variant.id}">${variant.display_name} (Tồn: ${variant.stock_quantity})</option>`
                            ).join('');
                        variantSelection.style.display = 'block';
                    } else {
                        variantSelection.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Error loading variants:', error);
                    variantSelection.style.display = 'none';
                }

            } else {
                brandCell.textContent = '-';
                priceInput.value = '';
                variantSelection.style.display = 'none';
            }

            calculateRowTotal(selectElement);
        }

        function calculateRowTotal(element) {
            const row = element.closest('tr');
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = quantity * price;

            row.querySelector('.total-cell').textContent = formatCurrency(total);
            updateGrandTotal();
        }

        function updateGrandTotal() {
            const totalCells = document.querySelectorAll('.total-cell');
            let grandTotal = 0;

            totalCells.forEach(cell => {
                const value = parseFloat(cell.textContent.replace(/[₫,\s]/g, '')) || 0;
                grandTotal += value;
            });

            document.getElementById('grandTotal').textContent = formatCurrency(grandTotal);
        }

        function removeRow(button) {
            const row = button.closest('tr');
            row.remove();
            updateRemoveButtons();
            updateGrandTotal();
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.item-row');
            const removeButtons = document.querySelectorAll('.remove-row');

            removeButtons.forEach(button => {
                button.disabled = rows.length <= 1;
            });
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0
            }).format(amount).replace('₫', '') + ' ₫';
        }

        function resetImportForm() {
            if (confirm('Bạn có chắc muốn làm mới form? Tất cả dữ liệu đã nhập sẽ bị xóa.')) {
                document.getElementById('importForm').reset();
                resetProductList();
                rowCounter = 0;
            }
        }
        function showLoading(message) {

            console.log('Loading:', message);
        }

        function hideLoading() {

            console.log('Loading completed');
        }

        function showMessage(message, type) {

            console.log(`${type}: ${message}`);
        }
    </script>
@endsection
