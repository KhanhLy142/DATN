document.addEventListener('DOMContentLoaded', function () {
    // =================== SỬA DANH MỤC ===================
    window.editCategory = function(name, desc) {
        document.getElementById('categoryName').value = name;
        document.getElementById('categoryDesc').value = desc;
    };

    // =================== SỬA THƯƠNG HIỆU ===================
    window.editBrand = function(name, desc) {
        document.getElementById('brandName').value = name;
        document.getElementById('brandDesc').value = desc;
    };

    // =================== TÌM KIẾM DANH MỤC ===================
    const searchCategoryInput = document.getElementById('searchInput');
    if (searchCategoryInput) {
        searchCategoryInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('table#categoryTable tbody tr');
            tableRows.forEach(row => {
                const name = row.cells[1]?.textContent.toLowerCase() || '';
                const desc = row.cells[2]?.textContent.toLowerCase() || '';
                row.style.display = (name.includes(keyword) || desc.includes(keyword)) ? '' : 'none';
            });
        });
    }

    // =================== TÌM KIẾM THƯƠNG HIỆU ===================
    const searchBrandInput = document.getElementById('searchBrandInput');
    if (searchBrandInput) {
        searchBrandInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('table#brandTable tbody tr');
            tableRows.forEach(row => {
                const name = row.cells[1]?.textContent.toLowerCase() || '';
                const desc = row.cells[2]?.textContent.toLowerCase() || '';
                row.style.display = (name.includes(keyword) || desc.includes(keyword)) ? '' : 'none';
            });
        });
    }

    // =================== HIỆN/ẨN THUỘC TÍNH THEO DANH MỤC ===================
    const categorySelect = document.getElementById('category');
    const toggles = document.querySelectorAll('.toggle-attribute');

    function updateAttributeVisibility() {
        const selected = categorySelect?.value;
        if (!selected) return;

        toggles.forEach(el => {
            const showIfList = (el.dataset.showIf || '').split(',').map(item => item.trim());
            el.style.display = showIfList.includes(selected) ? 'block' : 'none';
        });
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', updateAttributeVisibility);
        updateAttributeVisibility(); // run on page load
    }

    // =================== PREVIEW LOGO THƯƠNG HIỆU ===================
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('logo-preview');
            const previewImage = document.getElementById('preview-image');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    }

    // =================== TOGGLE PASSWORD VISIBILITY - IMPROVED ===================
    function initPasswordToggle(toggleBtnId, passwordFieldId, eyeIconId) {
        const toggleBtn = document.getElementById(toggleBtnId);
        const passwordField = document.getElementById(passwordFieldId);
        const eyeIcon = document.getElementById(eyeIconId);

        if (toggleBtn && passwordField && eyeIcon) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    eyeIcon.classList.remove('bi-eye');
                    eyeIcon.classList.add('bi-eye-slash');
                    toggleBtn.setAttribute('title', 'Ẩn mật khẩu');

                    eyeIcon.style.opacity = '0.6';
                    setTimeout(() => {
                        eyeIcon.style.opacity = '1';
                    }, 150);

                } else {
                    passwordField.type = 'password';
                    eyeIcon.classList.remove('bi-eye-slash');
                    eyeIcon.classList.add('bi-eye');
                    toggleBtn.setAttribute('title', 'Hiển thị mật khẩu');

                    eyeIcon.style.opacity = '0.6';
                    setTimeout(() => {
                        eyeIcon.style.opacity = '1';
                    }, 150);
                }
            });

            toggleBtn.style.cursor = 'pointer';
            toggleBtn.setAttribute('title', 'Hiển thị mật khẩu');
        }
    }

    initPasswordToggle('togglePassword', 'password', 'eyeIcon');
    initPasswordToggle('togglePasswordConfirm', 'password_confirmation', 'eyeIconConfirm');
    initPasswordToggle('toggleCurrentPassword', 'current_password', 'eyeIconCurrent');

    // =================== QUẢN LÝ THÔNG BÁO ===================
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.classList.contains('show')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });

    alerts.forEach(function(alert) {
        alert.style.transform = 'translateY(-20px)';
        alert.style.opacity = '0';

        setTimeout(function() {
            alert.style.transition = 'all 0.3s ease-in-out';
            alert.style.transform = 'translateY(0)';
            alert.style.opacity = '1';
        }, 100);
    });

    // =================== QUẢN LÝ SẢN PHẨM ===================
    const productNameInput = document.querySelector('input[name="name"]');
    const productSkuInput = document.querySelector('input[name="sku"]');

    if (productNameInput && productSkuInput) {
        productNameInput.addEventListener('input', function() {
            const name = this.value;
            const sku = name.toUpperCase()
                .replace(/[^A-Z0-9\s]/g, '')
                .replace(/\s+/g, '-')
                .substring(0, 20);
            productSkuInput.value = sku;
        });
    }

    // =================== PREVIEW ẢNH SẢN PHẨM ===================
    const productImageInput = document.querySelector('input[name="image"]');

    if (productImageInput) {
        productImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const existingPreview = document.getElementById('image-preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }

                    const preview = document.createElement('div');
                    preview.id = 'image-preview';
                    preview.className = 'mt-2';
                    preview.innerHTML = `
                        <small class="text-muted">Ảnh được chọn:</small><br>
                        <img src="${e.target.result}" alt="Preview" width="100" class="img-thumbnail">
                    `;
                    productImageInput.parentNode.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // =================== QUẢN LÝ BIẾN THỂ SẢN PHẨM ===================
    let variantIndex = 1;

    window.addVariant = function() {
        const container = document.getElementById('variant-container');
        if (!container) return;

        const newVariant = document.createElement('div');
        newVariant.classList.add('variant-group', 'border', 'p-3', 'rounded-3', 'mb-3', 'bg-light');

        newVariant.innerHTML = `
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Tên biến thể</label>
                    <input type="text" name="variants[${variantIndex}][variant_name]" class="form-control" placeholder="VD: Son đỏ 3g">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Màu sắc</label>
                    <input type="text" name="variants[${variantIndex}][color]" class="form-control" placeholder="Đỏ, Hồng...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Dung tích</label>
                    <input type="text" name="variants[${variantIndex}][volume]" class="form-control" placeholder="3g, 50ml...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mùi hương</label>
                    <input type="text" name="variants[${variantIndex}][scent]" class="form-control" placeholder="Hương hoa hồng...">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Giá <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">₫</span>
                        <input type="number" name="variants[${variantIndex}][price]"
                               class="form-control"
                               step="1000" min="0" placeholder="0">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tồn kho</label>
                    <input type="number" name="variants[${variantIndex}][stock_quantity]"
                           class="form-control"
                           min="0" value="0" placeholder="0">
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariant(this)">
                        <i class="bi bi-trash"></i> Xóa biến thể
                    </button>
                </div>
            </div>
        `;

        container.appendChild(newVariant);
        variantIndex++;
    };

    window.removeVariant = function(button) {
        const variantGroup = button.closest('.variant-group');
        const allVariants = document.querySelectorAll('.variant-group');

        if (allVariants.length > 1) {
            variantGroup.remove();
        } else {
            alert('Phải có ít nhất một biến thể!');
        }
    };

    // =================== PREVIEW ẢNH CHO TRANG EDIT ===================
    const editImageInput = document.getElementById('image');

    if (editImageInput) {
        editImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const existingPreview = document.getElementById('image-preview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }

                    const preview = document.createElement('div');
                    preview.id = 'image-preview';
                    preview.className = 'mt-2';
                    preview.innerHTML = `
                        <small class="text-muted">Ảnh mới được chọn:</small><br>
                        <img src="${e.target.result}" alt="Preview" width="100" class="img-thumbnail">
                    `;
                    editImageInput.parentNode.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // =================== AUTO-GENERATE SKU CHO TRANG EDIT ===================
    const editNameInput = document.getElementById('name');
    const editSkuInput = document.getElementById('sku');

    if (editNameInput && editSkuInput) {
        editNameInput.addEventListener('input', function() {
            const name = this.value;
            const sku = name.toUpperCase()
                .replace(/[^A-Z0-9\s]/g, '')
                .replace(/\s+/g, '-')
                .substring(0, 20);
            editSkuInput.value = sku;
        });
    }

    // =================== VALIDATION SỐ ĐIỆN THOẠI ===================
    const phoneInputs = document.querySelectorAll('input[name="phone"]');
    phoneInputs.forEach(function(phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length > 11) {
                this.value = this.value.substring(0, 11);
            }
        });

        phoneInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numbersOnly = paste.replace(/[^0-9]/g, '');
            if (numbersOnly.length <= 11) {
                this.value = numbersOnly;
            }
        });
    });

    // =================== VALIDATION MẬT KHẨU ===================
    function validatePassword(passwordField) {
        const password = passwordField.value;
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasNonalphas = /\W/.test(password);

        if (password.length >= minLength && hasUpperCase && hasLowerCase && hasNumbers && hasNonalphas) {
            passwordField.classList.remove('is-invalid');
            passwordField.classList.add('is-valid');
            return true;
        } else if (password.length > 0) {
            passwordField.classList.remove('is-valid');
            passwordField.classList.add('is-invalid');
            return false;
        } else {
            passwordField.classList.remove('is-invalid', 'is-valid');
            return true;
        }
    }

    const passwordField = document.getElementById('password');
    if (passwordField) {
        passwordField.addEventListener('input', function() {
            if (this.value.length > 0 || this.hasAttribute('required')) {
                validatePassword(this);
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    }

    // =================== QUẢN LÝ TỒN KHO ===================
    let rowIndex = 1;

    window.addInventoryRow = function() {
        const tbody = document.querySelector('#itemsTable tbody');
        if (!tbody) return;

        const newRow = createNewInventoryRow(rowIndex);
        tbody.appendChild(newRow);
        rowIndex++;
        updateRemoveButtons();
    };

    function createNewInventoryRow(index) {
        const row = document.createElement('tr');
        row.className = 'item-row new-item';

        const firstSelect = document.querySelector('.product-select');
        let productOptions = '<option value="">-- Chọn sản phẩm --</option>';

        if (firstSelect) {
            const options = firstSelect.querySelectorAll('option');
            options.forEach(option => {
                if (option.value) {
                    productOptions += `<option value="${option.value}" data-name="${option.dataset.name || ''}" data-sku="${option.dataset.sku || ''}">${option.textContent}</option>`;
                }
            });
        }

        row.innerHTML = `
            <td style="position: relative;">
                <select name="items[${index}][product_id]" class="form-select product-select" required>
                    ${productOptions}
                </select>
            </td>
            <td>
                <input type="number" name="items[${index}][quantity]"
                       class="form-control quantity-input text-center"
                       min="1" max="999999" placeholder="0" required>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" name="items[${index}][unit_price]"
                           class="form-control price-input text-end"
                           min="0" step="1000" placeholder="0" required>
                    <span class="input-group-text">₫</span>
                </div>
            </td>
            <td>
                <div class="fw-bold text-end total-cell">0 ₫</div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm remove-row"
                        style="width: 35px; height: 35px;">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        attachInventoryRowEvents(row);

        const newSelect = row.querySelector('.product-select');
        if (newSelect) {
            newSelect.addEventListener('change', function() {
                showSelectedProduct(this, index);
            });
        }

        return row;
    }

    function attachInventoryRowEvents(row) {
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const removeBtn = row.querySelector('.remove-row');

        [quantityInput, priceInput].forEach(input => {
            if (input) {
                input.addEventListener('input', calculateInventoryRowTotal);
            }
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                row.remove();
                updateRemoveButtons();
                calculateInventoryGrandTotal();
            });
        }
    }

    function calculateInventoryRowTotal(event) {
        const row = event.target.closest('.item-row');
        if (!row) return;

        const quantity = parseFloat(row.querySelector('.quantity-input')?.value) || 0;
        const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
        const total = quantity * price;

        const totalCell = row.querySelector('.total-cell');
        if (totalCell) {
            totalCell.textContent = formatInventoryCurrency(total);

            totalCell.style.animation = 'none';
            totalCell.offsetHeight;
            totalCell.style.animation = 'pulse 0.5s ease';
        }
        calculateInventoryGrandTotal();
    }

    function calculateInventoryGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity-input')?.value) || 0;
            const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
            grandTotal += quantity * price;
        });

        const grandTotalElement = document.getElementById('grandTotal');
        if (grandTotalElement) {
            grandTotalElement.textContent = formatInventoryCurrency(grandTotal);

            grandTotalElement.style.animation = 'none';
            grandTotalElement.offsetHeight;
            grandTotalElement.style.animation = 'pulse 1s ease';
        }
    }

    function formatInventoryCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row) => {
            const removeBtn = row.querySelector('.remove-row');
            if (removeBtn) {
                removeBtn.disabled = rows.length === 1;
            }
        });
    }

    function initInventoryForm() {
        const addRowBtn = document.getElementById('addRowBtn');
        if (addRowBtn) {
            addRowBtn.addEventListener('click', function() {
                addInventoryRow();
            });
        }

        const firstRow = document.querySelector('.item-row');
        if (firstRow) {
            attachInventoryRowEvents(firstRow);
        }

        const firstSelect = document.querySelector('.product-select');
        if (firstSelect) {
            firstSelect.addEventListener('change', function() {
                showSelectedProduct(this, 0);
            });
        }

        updateRemoveButtons();
    }

    // =================== TÌM KIẾM INVENTORY ===================
    const searchInventoryInput = document.getElementById('searchInventoryInput');
    if (searchInventoryInput) {
        searchInventoryInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('table.inventory-table tbody tr');
            tableRows.forEach(row => {
                const productName = row.cells[2]?.textContent.toLowerCase() || '';
                const sku = row.cells[3]?.textContent.toLowerCase() || '';
                const category = row.cells[4]?.textContent.toLowerCase() || '';
                const brand = row.cells[5]?.textContent.toLowerCase() || '';

                const isMatch = productName.includes(keyword) ||
                    sku.includes(keyword) ||
                    category.includes(keyword) ||
                    brand.includes(keyword);

                row.style.display = isMatch ? '' : 'none';
            });
        });
    }

    // =================== FILTER INVENTORY ===================
    const inventoryFilters = document.querySelectorAll('.inventory-filter');
    inventoryFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });

    // =================== HIỂN THỊ TÊN SẢN PHẨM SAU KHI CHỌN ===================
    window.showSelectedProduct = function(selectEl, index) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        if (!selectedOption.value) return;

        const productText = selectedOption.textContent.trim();
        const parts = productText.split(' - ');
        const productName = parts[0] || '';
        const productSku = parts[1] || '';

        selectEl.style.display = 'none';

        let displayEl = selectEl.parentNode.querySelector('.selected-product-display');

        if (!displayEl) {
            displayEl = document.createElement('div');
            displayEl.className = 'selected-product-display';
            displayEl.style.display = 'none';
            displayEl.innerHTML = `
                <div class="product-info">
                    <div class="product-name-display"></div>
                    <div class="product-sku-display"></div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary change-product-btn"
                        onclick="changeProduct(${index})">
                    <i class="bi bi-pencil"></i> Đổi
                </button>
            `;
            selectEl.parentNode.appendChild(displayEl);
        }

        const nameEl = displayEl.querySelector('.product-name-display');
        const skuEl = displayEl.querySelector('.product-sku-display');

        if (nameEl) nameEl.textContent = productName;
        if (skuEl) skuEl.textContent = productSku ? `SKU: ${productSku}` : '';

        displayEl.style.display = 'flex';
        displayEl.style.animation = 'slideInDown 0.3s ease';
    };

    window.changeProduct = function(index) {
        const row = document.querySelector(`select[name="items[${index}][product_id]"]`)?.closest('.item-row');
        if (!row) return;

        const selectEl = row.querySelector('.product-select');
        const displayEl = row.querySelector('.selected-product-display');

        if (selectEl) selectEl.style.display = 'block';
        if (displayEl) displayEl.style.display = 'none';

        if (selectEl) selectEl.value = '';

        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const totalCell = row.querySelector('.total-cell');

        if (quantityInput) quantityInput.value = '';
        if (priceInput) priceInput.value = '';
        if (totalCell) totalCell.textContent = '0 ₫';

        calculateInventoryGrandTotal();

        if (selectEl) selectEl.focus();
    };

    // =================== QUẢN LÝ ĐƠN HÀNG (ORDERS) - SỬA LẠI ===================
    function initOrderManagement() {
        let orderItemCount = 1;

        // Hàm định dạng tiền tệ
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
        }

        // Hàm tính tổng tiền đơn hàng
        function calculateOrderTotal() {
            let total = 0;
            let hasValidItems = false;

            document.querySelectorAll('.order-product-item').forEach(item => {
                const productSelect = item.querySelector('.order-product-select');
                const quantityInput = item.querySelector('.order-quantity-input');

                if (productSelect && productSelect.value && quantityInput && quantityInput.value) {
                    const selectedOption = productSelect.selectedOptions[0];
                    if (selectedOption && selectedOption.dataset.price) {
                        const price = parseFloat(selectedOption.dataset.price);
                        const quantity = parseInt(quantityInput.value) || 0;
                        if (quantity > 0) {
                            total += price * quantity;
                            hasValidItems = true;
                        }
                    }
                }
            });

            const totalElement = document.getElementById('orderTotalAmount');
            if (totalElement) {
                totalElement.textContent = formatMoney(total);
            }

            const submitBtn = document.getElementById('orderSubmitBtn');
            if (submitBtn) {
                submitBtn.disabled = !hasValidItems || total <= 0;
            }
        }

        // Hàm cập nhật thông tin sản phẩm trong đơn hàng
        function updateOrderItemInfo(item) {
            const productSelect = item.querySelector('.order-product-select');
            const priceInput = item.querySelector('.order-item-price');
            const quantityInput = item.querySelector('.order-quantity-input');
            const subtotalSpan = item.querySelector('.order-item-subtotal');

            if (productSelect && productSelect.value) {
                const selectedOption = productSelect.selectedOptions[0];
                const price = parseFloat(selectedOption.dataset.price || 0);
                const stock = parseInt(selectedOption.dataset.stock || 0);
                const quantity = parseInt(quantityInput.value) || 1;

                // Cập nhật đơn giá
                if (priceInput) {
                    priceInput.value = formatMoney(price);
                }

                // Cập nhật số lượng tối đa
                if (quantityInput) {
                    quantityInput.setAttribute('max', stock);
                }

                // Cập nhật thành tiền
                const subtotal = price * quantity;
                if (subtotalSpan) {
                    subtotalSpan.textContent = formatMoney(subtotal);
                }

                // Kiểm tra số lượng không vượt quá tồn kho
                if (quantity > stock) {
                    quantityInput.value = stock;
                    alert(`Số lượng không được vượt quá ${stock}`);
                }

                // Xóa thông báo lỗi cũ
                const existingError = item.querySelector('.stock-error');
                if (existingError) {
                    existingError.remove();
                }

                // Hiển thị cảnh báo nếu vượt quá tồn kho
                if (quantity > stock) {
                    quantityInput.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback stock-error';
                    errorDiv.textContent = `Chỉ còn ${stock} sản phẩm trong kho`;
                    quantityInput.parentNode.appendChild(errorDiv);
                } else {
                    quantityInput.classList.remove('is-invalid');
                }
            } else {
                // Reset khi không chọn sản phẩm
                if (priceInput) priceInput.value = '';
                if (subtotalSpan) subtotalSpan.textContent = '0đ';
                if (quantityInput) quantityInput.removeAttribute('max');
            }

            calculateOrderTotal();
        }

        // Gán sự kiện cho item trong đơn hàng
        function bindOrderItemEvents(item) {
            const productSelect = item.querySelector('.order-product-select');
            const quantityInput = item.querySelector('.order-quantity-input');

            if (productSelect) {
                productSelect.addEventListener('change', function() {
                    updateOrderItemInfo(item);
                });
            }

            if (quantityInput) {
                quantityInput.addEventListener('input', function() {
                    updateOrderItemInfo(item);
                });
            }
        }

        // Thêm sản phẩm mới vào đơn hàng
        const addOrderProductBtn = document.getElementById('addOrderProduct');
        if (addOrderProductBtn) {
            addOrderProductBtn.addEventListener('click', function() {
                const productItems = document.getElementById('orderProductItems');
                const firstItem = document.querySelector('.order-product-item');

                if (firstItem && productItems) {
                    const newItem = firstItem.cloneNode(true);

                    // Cập nhật names và reset values
                    const newSelect = newItem.querySelector('.order-product-select');
                    const newQuantity = newItem.querySelector('.order-quantity-input');
                    const newPrice = newItem.querySelector('.order-item-price');
                    const newSubtotal = newItem.querySelector('.order-item-subtotal');

                    if (newSelect) {
                        newSelect.name = `items[${orderItemCount}][product_id]`;
                        newSelect.value = '';
                    }
                    if (newQuantity) {
                        newQuantity.name = `items[${orderItemCount}][quantity]`;
                        newQuantity.value = '1';
                    }
                    if (newPrice) {
                        newPrice.value = '';
                    }
                    if (newSubtotal) {
                        newSubtotal.textContent = '0đ';
                    }

                    // Xóa các thông báo lỗi cũ
                    newItem.querySelectorAll('.invalid-feedback').forEach(feedback => feedback.remove());
                    newItem.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                    // Thêm nút xóa nếu chưa có
                    let removeBtn = newItem.querySelector('.remove-product-btn');
                    if (!removeBtn) {
                        removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 remove-product-btn';
                        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                        removeBtn.title = 'Xóa sản phẩm';
                        newItem.style.position = 'relative';
                        newItem.appendChild(removeBtn);
                    }

                    removeBtn.addEventListener('click', function() {
                        newItem.remove();
                        calculateOrderTotal();
                    });

                    productItems.appendChild(newItem);
                    orderItemCount++;

                    // Gán sự kiện cho item mới
                    bindOrderItemEvents(newItem);
                }
            });
        }

        // Gán sự kiện cho item đầu tiên
        const firstOrderItem = document.querySelector('.order-product-item');
        if (firstOrderItem) {
            bindOrderItemEvents(firstOrderItem);

            // Khởi tạo tính toán ban đầu
            updateOrderItemInfo(firstOrderItem);
        }

        // Gán sự kiện cho tất cả các item hiện có (trong trường hợp edit)
        document.querySelectorAll('.order-product-item').forEach(item => {
            bindOrderItemEvents(item);
        });

        // Tính toán ban đầu
        calculateOrderTotal();
    }

    // =================== TÌM KIẾM KHÁCH HÀNG ===================
    const searchCustomerInput = document.getElementById('searchCustomerInput');
    if (searchCustomerInput) {
        searchCustomerInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('table#customerTable tbody tr');
            tableRows.forEach(row => {
                const name = row.cells[1]?.textContent.toLowerCase() || '';
                const email = row.cells[2]?.textContent.toLowerCase() || '';
                const phone = row.cells[3]?.textContent.toLowerCase() || '';

                const isMatch = name.includes(keyword) ||
                    email.includes(keyword) ||
                    phone.includes(keyword);

                row.style.display = isMatch ? '' : 'none';
            });
        });
    }

    // =================== FILTER VÀ SEARCH ĐƠN HÀNG ===================
    function initOrderSearch() {
        const searchOrderInput = document.getElementById('searchOrderInput');
        if (searchOrderInput) {
            searchOrderInput.addEventListener('input', function() {
                const keyword = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('table tbody tr');

                tableRows.forEach(row => {
                    const orderId = row.cells[1]?.textContent.toLowerCase() || '';
                    const customerName = row.cells[2]?.textContent.toLowerCase() || '';
                    const phone = row.cells[3]?.textContent.toLowerCase() || '';

                    const isMatch = orderId.includes(keyword) ||
                        customerName.includes(keyword) ||
                        phone.includes(keyword);

                    row.style.display = isMatch ? '' : 'none';
                });
            });
        }
    }

    function initOrderStatusFilter() {
        const statusFilter = document.querySelector('select[name="status"]');
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                // Form sẽ tự submit khi thay đổi
            });
        }
    }

    // =================== XÁC NHẬN XÓA ===================
    function initDeleteConfirmations() {
        // Xác nhận xóa đơn hàng
        const deleteOrderForms = document.querySelectorAll('form[action*="orders"][action*="cancel"]');
        deleteOrderForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Hành động này không thể hoàn tác!')) {
                    e.preventDefault();
                }
            });
        });

        // Xác nhận xóa khách hàng
        const deleteCustomerForms = document.querySelectorAll('form[action*="customers"][method="POST"]');
        deleteCustomerForms.forEach(form => {
            const deleteInput = form.querySelector('input[name="_method"][value="DELETE"]');
            if (deleteInput) {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Bạn có chắc chắn muốn xóa khách hàng này? Hành động này không thể hoàn tác!')) {
                        e.preventDefault();
                    }
                });
            }
        });

        // Xác nhận xóa sản phẩm
        const deleteProductForms = document.querySelectorAll('form[action*="products"][method="POST"]');
        deleteProductForms.forEach(form => {
            const deleteInput = form.querySelector('input[name="_method"][value="DELETE"]');
            if (deleteInput) {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác!')) {
                        e.preventDefault();
                    }
                });
            }
        });

        // Xác nhận xóa mã giảm giá
        const deleteDiscountForms = document.querySelectorAll('form[action*="discounts"][method="POST"]');
        deleteDiscountForms.forEach(form => {
            const deleteInput = form.querySelector('input[name="_method"][value="DELETE"]');
            if (deleteInput) {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Bạn có chắc chắn muốn xóa mã giảm giá này? Hành động này không thể hoàn tác!')) {
                        e.preventDefault();
                    }
                });
            }
        });
    }

    // =================== QUẢN LÝ MÃ GIẢM GIÁ ===================
    function initDiscountManagement() {
        console.log('Initializing discount management...');

        // Initialize Select2 cho dropdown sản phẩm
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2').select2({
                placeholder: 'Chọn sản phẩm...',
                allowClear: true,
                width: '100%'
            });
        }

        // Tự động tạo mã giảm giá
        const codeInput = document.getElementById('code');
        if (codeInput) {
            codeInput.addEventListener('focus', function() {
                if (this.value === '') {
                    const randomCode = 'DISC' + Math.random().toString(36).substr(2, 6).toUpperCase();
                    this.value = randomCode;
                }
            });

            // Tự động chuyển thành chữ hoa
            codeInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }

        // Cập nhật placeholder cho discount value dựa trên loại
        const discountTypeSelect = document.getElementById('discount_type');
        const discountValueInput = document.getElementById('discount_value');

        if (discountTypeSelect && discountValueInput) {
            function updateDiscountValuePlaceholder() {
                const type = discountTypeSelect.value;

                if (type === 'percent') {
                    discountValueInput.setAttribute('placeholder', 'Ví dụ: 15 (tương đương 15%)');
                    discountValueInput.setAttribute('max', '100');
                    discountValueInput.setAttribute('step', '0.1');
                } else if (type === 'fixed') {
                    discountValueInput.setAttribute('placeholder', 'Ví dụ: 50000 (tương đương 50,000 VNĐ)');
                    discountValueInput.removeAttribute('max');
                    discountValueInput.setAttribute('step', '1000');
                }
            }

            discountTypeSelect.addEventListener('change', updateDiscountValuePlaceholder);
            updateDiscountValuePlaceholder(); // Chạy lần đầu
        }

        // Validation cho discount value
        if (discountValueInput && discountTypeSelect) {
            discountValueInput.addEventListener('input', function() {
                const type = discountTypeSelect.value;
                const value = parseFloat(this.value);

                // Xóa validation cũ
                this.classList.remove('is-invalid');
                const existingFeedback = this.parentNode.querySelector('.invalid-feedback');
                if (existingFeedback) {
                    existingFeedback.remove();
                }

                // Validation cho phần trăm
                if (type === 'percent' && value > 100) {
                    this.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Giá trị giảm giá phần trăm không được vượt quá 100%';
                    this.parentNode.appendChild(feedback);
                }

                // Validation cho giá trị âm
                if (value < 0) {
                    this.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Giá trị giảm giá không được âm';
                    this.parentNode.appendChild(feedback);
                }
            });
        }

        // Validation cho ngày
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        if (startDateInput && endDateInput) {
            function validateDates() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // Reset validation
                [startDateInput, endDateInput].forEach(input => {
                    input.classList.remove('is-invalid');
                    const feedback = input.parentNode.querySelector('.invalid-feedback');
                    if (feedback) feedback.remove();
                });

                // Kiểm tra ngày bắt đầu không được trong quá khứ (chỉ cho form tạo mới)
                if (window.location.pathname.includes('create') && startDate < today) {
                    startDateInput.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Ngày bắt đầu không được nhỏ hơn ngày hiện tại';
                    startDateInput.parentNode.appendChild(feedback);
                }

                // Kiểm tra ngày kết thúc phải sau ngày bắt đầu
                if (endDate <= startDate) {
                    endDateInput.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Ngày kết thúc phải sau ngày bắt đầu';
                    endDateInput.parentNode.appendChild(feedback);
                }
            }

            startDateInput.addEventListener('change', validateDates);
            endDateInput.addEventListener('change', validateDates);
        }

        // Copy discount code functionality
        window.copyDiscountCode = function() {
            const codeElement = document.querySelector('[data-discount-code]');
            if (codeElement) {
                const code = codeElement.textContent || codeElement.dataset.discountCode;
                navigator.clipboard.writeText(code).then(function() {
                    // Hiển thị thông báo thành công
                    showToast('Đã sao chép mã giảm giá!', 'success');
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                    // Fallback: select text
                    if (codeElement.select) {
                        codeElement.select();
                    }
                });
            }
        };

        // Tìm kiếm mã giảm giá
        const searchDiscountInput = document.getElementById('searchDiscountInput');
        if (searchDiscountInput) {
            searchDiscountInput.addEventListener('input', function() {
                const keyword = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('table tbody tr');

                tableRows.forEach(row => {
                    const code = row.cells[0]?.textContent.toLowerCase() || '';
                    const type = row.cells[1]?.textContent.toLowerCase() || '';
                    const value = row.cells[2]?.textContent.toLowerCase() || '';

                    const isMatch = code.includes(keyword) ||
                        type.includes(keyword) ||
                        value.includes(keyword);

                    row.style.display = isMatch ? '' : 'none';
                });
            });
        }

        // Filter mã giảm giá theo trạng thái và loại
        const discountFilters = document.querySelectorAll('.discount-filter');
        discountFilters.forEach(filter => {
            filter.addEventListener('change', function() {
                // Submit form để filter
                const form = this.closest('form');
                if (form) {
                    form.submit();
                }
            });
        });

        // Xử lý toggle status cho mã giảm giá
        const toggleStatusForms = document.querySelectorAll('form[action*="toggle-status"]');
        toggleStatusForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = form.querySelector('button[type="submit"]');
                const isActive = button.classList.contains('btn-secondary'); // Nếu là secondary thì đang active
                const action = isActive ? 'vô hiệu hóa' : 'kích hoạt';

                if (!confirm(`Bạn có chắc muốn ${action} mã giảm giá này?`)) {
                    e.preventDefault();
                }
            });
        });

        console.log('Discount management initialized');
    }

    // =================== HELPER FUNCTIONS ===================
    function showToast(message, type = 'info') {
        // Tạo toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.3s ease;
        `;

        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(toast);

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    // =================== QUẢN LÝ THANH TOÁN ===================
    function initPaymentManagement() {
        console.log('Initializing payment management...');
        // Chỉ cần xác nhận xóa thôi, không cần modal hoàn tiền
        console.log('Payment management initialized - Simple version');
    }

    // =================== KHỞI TẠO TẤT CẢ CHỨC NĂNG ===================
    function initAllFunctions() {
        // Khởi tạo inventory form
        initInventoryForm();

        // Khởi tạo order management nếu đang ở trang đơn hàng
        if (document.getElementById('orderForm') ||
            document.querySelector('.order-product-item') ||
            window.location.pathname.includes('orders')) {

            console.log('Initializing order management...');
            initOrderManagement();
            initOrderSearch();
            initOrderStatusFilter();
        }

        // Khởi tạo discount management nếu đang ở trang mã giảm giá
        if (window.location.pathname.includes('discounts') ||
            document.querySelector('.discount-filter') ||
            document.getElementById('code') ||
            document.getElementById('discount_type')) {

            console.log('Initializing discount management...');
            initDiscountManagement();
        }

        // Khởi tạo payment management nếu đang ở trang thanh toán
        if (window.location.pathname.includes('payments')) {
            initPaymentManagement();
        }

        // Khởi tạo xác nhận xóa
        initDeleteConfirmations();

        console.log('All admin functions initialized');
    }

    // Gọi hàm khởi tạo
    initAllFunctions();
});

function initDiscountManagement() {
    console.log('Initializing discount management...');

    // Initialize Select2 cho dropdown sản phẩm với tìm kiếm
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('#products').select2({
            placeholder: 'Tìm kiếm và chọn sản phẩm...',
            allowClear: true,
            width: '100%',
            multiple: true,
            language: {
                noResults: function() {
                    return 'Không tìm thấy sản phẩm nào';
                },
                searching: function() {
                    return 'Đang tìm kiếm...';
                },
                removeAllItems: function() {
                    return 'Xóa tất cả';
                }
            }
        });

        // Xử lý khi chọn/bỏ chọn sản phẩm
        $('#products').on('change', function() {
            updateSelectedProductsPreview();
        });
    }

    // Tự động tạo mã giảm giá
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('focus', function() {
            if (this.value === '') {
                const randomCode = 'DISC' + Math.random().toString(36).substr(2, 6).toUpperCase();
                this.value = randomCode;
            }
        });

        // Tự động chuyển thành chữ hoa
        codeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    }

    // Cập nhật placeholder cho discount value dựa trên loại
    const discountTypeSelect = document.getElementById('discount_type');
    const discountValueInput = document.getElementById('discount_value');

    if (discountTypeSelect && discountValueInput) {
        function updateDiscountValuePlaceholder() {
            const type = discountTypeSelect.value;

            if (type === 'percent') {
                discountValueInput.setAttribute('placeholder', 'Ví dụ: 15 (tương đương 15%)');
                discountValueInput.setAttribute('max', '100');
                discountValueInput.setAttribute('step', '0.1');
            } else if (type === 'fixed') {
                discountValueInput.setAttribute('placeholder', 'Ví dụ: 50000 (tương đương 50,000 VNĐ)');
                discountValueInput.removeAttribute('max');
                discountValueInput.setAttribute('step', '1000');
            }
        }

        discountTypeSelect.addEventListener('change', updateDiscountValuePlaceholder);
        updateDiscountValuePlaceholder(); // Chạy lần đầu
    }

    // Validation cho discount value
    if (discountValueInput && discountTypeSelect) {
        discountValueInput.addEventListener('input', function() {
            const type = discountTypeSelect.value;
            const value = parseFloat(this.value);

            // Xóa validation cũ
            this.classList.remove('is-invalid');
            const existingFeedback = this.parentNode.querySelector('.invalid-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }

            // Validation cho phần trăm
            if (type === 'percent' && value > 100) {
                this.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Giá trị giảm giá phần trăm không được vượt quá 100%';
                this.parentNode.appendChild(feedback);
            }

            // Validation cho giá trị âm
            if (value < 0) {
                this.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Giá trị giảm giá không được âm';
                this.parentNode.appendChild(feedback);
            }
        });
    }

    // Validation cho ngày
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (startDateInput && endDateInput) {
        function validateDates() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Reset validation
            [startDateInput, endDateInput].forEach(input => {
                input.classList.remove('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) feedback.remove();
            });

            // Kiểm tra ngày bắt đầu không được trong quá khứ (chỉ cho form tạo mới)
            if (window.location.pathname.includes('create') && startDate < today) {
                startDateInput.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Ngày bắt đầu không được nhỏ hơn ngày hiện tại';
                startDateInput.parentNode.appendChild(feedback);
            }

            // Kiểm tra ngày kết thúc phải sau ngày bắt đầu
            if (endDate <= startDate) {
                endDateInput.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = 'Ngày kết thúc phải sau ngày bắt đầu';
                endDateInput.parentNode.appendChild(feedback);
            }
        }

        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);
    }

    // Copy discount code functionality
    window.copyDiscountCode = function() {
        const codeElement = document.querySelector('[data-discount-code]');
        if (codeElement) {
            const code = codeElement.textContent || codeElement.dataset.discountCode;
            navigator.clipboard.writeText(code).then(function() {
                // Hiển thị thông báo thành công
                showToast('Đã sao chép mã giảm giá: ' + code, 'success');
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                // Fallback: select text
                if (codeElement.select) {
                    codeElement.select();
                }
                showToast('Không thể sao chép tự động. Vui lòng copy thủ công!', 'warning');
            });
        }
    };

    // Tìm kiếm mã giảm giá
    const searchDiscountInput = document.getElementById('searchDiscountInput');
    if (searchDiscountInput) {
        searchDiscountInput.addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('table tbody tr');

            tableRows.forEach(row => {
                const code = row.cells[1]?.textContent.toLowerCase() || '';
                const type = row.cells[2]?.textContent.toLowerCase() || '';
                const value = row.cells[3]?.textContent.toLowerCase() || '';

                const isMatch = code.includes(keyword) ||
                    type.includes(keyword) ||
                    value.includes(keyword);

                row.style.display = isMatch ? '' : 'none';
            });
        });
    }

    // Filter mã giảm giá theo trạng thái và loại
    const discountFilters = document.querySelectorAll('.discount-filter');
    discountFilters.forEach(filter => {
        filter.addEventListener('change', function() {
            // Submit form để filter
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });

    // Xử lý toggle status cho mã giảm giá
    const toggleStatusForms = document.querySelectorAll('form[action*="toggle-status"]');
    toggleStatusForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('button[type="submit"]');
            const isActive = button.classList.contains('btn-secondary') || button.classList.contains('btn-outline-secondary');
            const action = isActive ? 'vô hiệu hóa' : 'kích hoạt';

            if (!confirm(`Bạn có chắc muốn ${action} mã giảm giá này?`)) {
                e.preventDefault();
            }
        });
    });

    console.log('Discount management initialized');
}

// Hàm cập nhật preview sản phẩm đã chọn
function updateSelectedProductsPreview() {
    const selectedOptions = $('#products').select2('data');
    const previewContainer = document.getElementById('selected-products-preview');
    const previewList = document.getElementById('selected-products-list');

    if (!previewContainer || !previewList) return;

    if (selectedOptions.length > 0) {
        previewContainer.style.display = 'block';
        previewList.innerHTML = '';

        selectedOptions.forEach(option => {
            const badge = document.createElement('span');
            badge.className = 'product-badge';
            badge.innerHTML = `
                <i class="bi bi-box"></i>
                <span>${option.text}</span>
                <span class="remove-product" onclick="removeSelectedProduct('${option.id}')">
                    <i class="bi bi-x"></i>
                </span>
            `;
            previewList.appendChild(badge);
        });
    } else {
        previewContainer.style.display = 'none';
    }
}

// Hàm xóa sản phẩm đã chọn từ preview
function removeSelectedProduct(productId) {
    const currentValues = $('#products').val() || [];
    const newValues = currentValues.filter(id => id !== productId);
    $('#products').val(newValues).trigger('change');
}

// Hàm hiển thị toast notification nâng cao
function showToast(message, type = 'info', duration = 3000) {
    // Xóa toast cũ nếu có
    const existingToast = document.querySelector('.custom-toast');
    if (existingToast) {
        existingToast.remove();
    }

    // Tạo toast notification với animation
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed custom-toast`;
    toast.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 350px;
        max-width: 500px;
        animation: slideInRight 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
        border-radius: 8px;
    `;

    let icon = '';
    switch(type) {
        case 'success':
            icon = '<i class="bi bi-check-circle-fill"></i>';
            break;
        case 'danger':
            icon = '<i class="bi bi-x-circle-fill"></i>';
            break;
        case 'warning':
            icon = '<i class="bi bi-exclamation-triangle-fill"></i>';
            break;
        default:
            icon = '<i class="bi bi-info-circle-fill"></i>';
    }

    toast.innerHTML = `
        <div class="d-flex align-items-center">
            ${icon}
            <span class="ms-2">${message}</span>
        </div>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    document.body.appendChild(toast);

    // Tự động ẩn sau thời gian được chỉ định
    setTimeout(() => {
        if (toast.parentNode) {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    }, duration);
}

// CSS Animation cho toast
if (!document.querySelector('#toast-animations')) {
    const style = document.createElement('style');
    style.id = 'toast-animations';
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .custom-toast {
            transition: all 0.3s ease;
        }

        .custom-toast:hover {
            transform: translateX(-5px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2) !important;
        }
    `;
    document.head.appendChild(style);
}
