document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;

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
            const firstSelect = firstRow.querySelector('.product-select');
            if (firstSelect) {
                firstSelect.addEventListener('change', function() {
                    handleProductSelection(this, 0);
                });
            }
        }

        updateRemoveButtons();
    }
    function handleProductSelection(selectElement, index) {
        const productId = selectElement.value;
        const row = selectElement.closest('.item-row');
        const variantContainer = row.querySelector('.variant-selection');
        const variantSelect = row.querySelector('.variant-select');

        if (!productId) {
            hideVariantSelection(variantContainer);
            return;
        }

        showSelectedProduct(selectElement, index);

        loadProductVariants(productId, variantContainer, variantSelect);
    }

    function loadProductVariants(productId, variantContainer, variantSelect) {
        if (!variantContainer || !variantSelect) return;

        variantSelect.innerHTML = '<option value="">Đang tải variants...</option>';
        variantContainer.style.display = 'block';

        fetch(`/admin/products/${productId}/variants`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(variants => {
                let options = '<option value="">-- Chọn biến thể (tùy chọn) --</option>';

                if (variants && variants.length > 0) {
                    variants.forEach(variant => {
                        const displayName = variant.variant_name ||
                            [variant.color, variant.volume, variant.scent]
                                .filter(v => v && v.trim() !== '')
                                .join(' - ') ||
                            `Biến thể ${variant.id}`;

                        const stockInfo = variant.stock_quantity !== undefined ?
                            ` (Tồn: ${variant.stock_quantity})` : '';

                        const priceInfo = variant.price ?
                            ` - ${formatCurrency(variant.price)}` : '';

                        options += `<option value="${variant.id}"
                                    data-stock="${variant.stock_quantity || 0}"
                                    data-price="${variant.price || 0}">
                            ${displayName}${priceInfo}${stockInfo}
                        </option>`;
                    });
                } else {
                    options = '<option value="">Sản phẩm này không có biến thể</option>';
                    setTimeout(() => {
                        variantContainer.style.display = 'none';
                    }, 2000);
                }

                variantSelect.innerHTML = options;
            })
            .catch(error => {
                console.error('Error loading variants:', error);
                variantSelect.innerHTML = '<option value="">Lỗi khi tải biến thể</option>';
                setTimeout(() => {
                    variantContainer.style.display = 'none';
                }, 3000);
            });
    }

    function hideVariantSelection(variantContainer) {
        if (variantContainer) {
            variantContainer.style.display = 'none';
            const variantSelect = variantContainer.querySelector('.variant-select');
            if (variantSelect) {
                variantSelect.innerHTML = '<option value="">-- Chọn biến thể --</option>';
            }
        }
    }

    window.showSelectedProduct = function(selectElement, index) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if (!selectedOption || !selectedOption.value) return;

        const productText = selectedOption.textContent.trim();
        const parts = productText.split(' - ');
        const productName = parts[0] || '';
        const productSku = parts[1] || '';

        selectElement.style.display = 'none';

        const parentTd = selectElement.closest('td');
        let displayElement = parentTd.querySelector('.selected-product-display');

        if (!displayElement) {
            displayElement = document.createElement('div');
            displayElement.className = 'selected-product-display';
            displayElement.style.cssText = `
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px;
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 6px;
                margin-top: 8px;
            `;

            displayElement.innerHTML = `
                <div class="product-info">
                    <div class="product-name-display fw-semibold"></div>
                    <div class="product-sku-display text-muted small"></div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary change-product-btn">
                    <i class="bi bi-pencil"></i> Đổi
                </button>
            `;

            parentTd.appendChild(displayElement);

            const changeBtn = displayElement.querySelector('.change-product-btn');
            changeBtn.addEventListener('click', function() {
                changeProduct(index);
            });
        }

        const nameElement = displayElement.querySelector('.product-name-display');
        const skuElement = displayElement.querySelector('.product-sku-display');

        if (nameElement) nameElement.textContent = productName;
        if (skuElement) skuElement.textContent = productSku ? `SKU: ${productSku}` : '';

        displayElement.style.display = 'flex';
    };

    window.changeProduct = function(index) {
        let row;

        if (typeof index === 'number') {
            row = document.querySelector(`select[name="items[${index}][product_id]"]`)?.closest('.item-row');
        } else {
            row = event.target.closest('.item-row');
        }

        if (!row) {
            console.error('Không tìm thấy row để thay đổi sản phẩm');
            return;
        }

        const selectElement = row.querySelector('.product-select');
        const displayElement = row.querySelector('.selected-product-display');
        const variantContainer = row.querySelector('.variant-selection');

        if (selectElement) {
            selectElement.style.display = 'block';
            selectElement.value = '';
            selectElement.focus();
        }

        if (displayElement) {
            displayElement.style.display = 'none';
        }

        hideVariantSelection(variantContainer);

        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const totalCell = row.querySelector('.total-cell');

        if (quantityInput) quantityInput.value = '';
        if (priceInput) priceInput.value = '';
        if (totalCell) totalCell.textContent = '0 ₫';

        calculateInventoryGrandTotal();
    };

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
                    productOptions += `<option value="${option.value}"
                        data-name="${option.dataset.name || ''}"
                        data-sku="${option.dataset.sku || ''}"
                    >${option.textContent}</option>`;
                }
            });
        }

        row.innerHTML = `
            <td style="position: relative;">
                <select name="items[${index}][product_id]" class="form-select product-select" required>
                    ${productOptions}
                </select>

                <div class="variant-selection mt-2" style="display: none;">
                    <label class="form-label fw-semibold">Chọn biến thể</label>
                    <select class="form-select variant-select" name="items[${index}][variant_id]">
                        <option value="">-- Chọn biến thể --</option>
                    </select>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Nếu không chọn biến thể, hệ thống sẽ phân bổ đều cho tất cả biến thể
                    </small>
                </div>
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
                handleProductSelection(this, index);
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
            totalCell.textContent = formatCurrency(total);
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
            grandTotalElement.textContent = formatCurrency(grandTotal);
        }
    }

    function formatCurrency(amount) {
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

    window.resetImportForm = function() {
        if (confirm('Bạn có chắc muốn làm mới form? Tất cả dữ liệu đã nhập sẽ bị xóa.')) {
            const supplierSelect = document.querySelector('select[name="supplier_id"]');
            if (supplierSelect) supplierSelect.value = '';
            const notesTextarea = document.querySelector('textarea[name="notes"]');
            if (notesTextarea) notesTextarea.value = '';
            const allRows = document.querySelectorAll('.item-row');
            allRows.forEach((row, index) => {
                if (index > 0) {
                    row.remove();
                }
            });
            const firstRow = document.querySelector('.item-row');
            if (firstRow) {
                const productSelect = firstRow.querySelector('.product-select');
                const quantityInput = firstRow.querySelector('.quantity-input');
                const priceInput = firstRow.querySelector('.price-input');
                const totalCell = firstRow.querySelector('.total-cell');
                const displayElement = firstRow.querySelector('.selected-product-display');
                const variantContainer = firstRow.querySelector('.variant-selection');

                if (productSelect) productSelect.value = '';
                if (quantityInput) quantityInput.value = '';
                if (priceInput) priceInput.value = '';
                if (totalCell) totalCell.textContent = '0 ₫';
                if (displayElement) displayElement.style.display = 'none';
                if (variantContainer) variantContainer.style.display = 'none';
                if (productSelect) productSelect.style.display = 'block';
            }

            calculateInventoryGrandTotal();
            updateRemoveButtons();
            rowIndex = 1;
        }
    };
    function initOrderManagement() {
        let orderItemCount = 1;

        function loadOrderProductVariants(productSelect) {
            const productId = productSelect.value;
            const orderItem = productSelect.closest('.order-product-item');
            const variantContainer = orderItem.querySelector('.variant-selection');
            const variantSelect = variantContainer.querySelector('.variant-select');

            if (!productId) {
                variantContainer.style.display = 'none';
                return;
            }

            fetch(`/admin/products/${productId}/variants`)
                .then(response => response.json())
                .then(variants => {
                    let options = '<option value="">-- Chọn biến thể (tùy chọn) --</option>';

                    if (variants.length > 0) {
                        variants.forEach(variant => {
                            const displayName = variant.variant_name ||
                                [variant.color, variant.volume, variant.scent].filter(v => v).join(' - ');

                            options += `<option value="${variant.id}"
                                    data-price="${variant.price}"
                                    data-stock="${variant.stock_quantity}">
                            ${displayName} - ${formatCurrency(variant.price)} (Còn: ${variant.stock_quantity})
                        </option>`;
                        });
                        variantContainer.style.display = 'block';
                    } else {
                        variantContainer.style.display = 'none';
                    }

                    variantSelect.innerHTML = options;
                })
                .catch(error => {
                    console.error('Error loading variants:', error);
                    variantContainer.style.display = 'none';
                });
        }

        function bindOrderEvents() {
            document.querySelectorAll('.order-product-select').forEach(select => {
                select.addEventListener('change', function() {
                    loadOrderProductVariants(this);
                    updateOrderItemInfo(this.closest('.order-product-item'));
                });
            });

            document.querySelectorAll('.variant-select, .order-quantity-input').forEach(element => {
                element.addEventListener('change', function() {
                    updateOrderItemInfo(this.closest('.order-product-item'));
                });
            });
        }

        function updateOrderItemInfo(orderItem) {
            const productSelect = orderItem.querySelector('.order-product-select');
            const variantSelect = orderItem.querySelector('.variant-select');
            const quantityInput = orderItem.querySelector('.order-quantity-input');
            const priceInput = orderItem.querySelector('.order-item-price');
            const subtotalSpan = orderItem.querySelector('.order-item-subtotal');

            if (!productSelect.value) return;

            let price = 0;
            let stock = 0;

            if (variantSelect.value) {
                const selectedVariant = variantSelect.options[variantSelect.selectedIndex];
                price = parseFloat(selectedVariant.dataset.price) || 0;
                stock = parseInt(selectedVariant.dataset.stock) || 0;
            } else {
                const selectedProduct = productSelect.options[productSelect.selectedIndex];
                price = parseFloat(selectedProduct.dataset.price) || 0;
                stock = parseInt(selectedProduct.dataset.stock) || 0;
            }

            const quantity = parseInt(quantityInput.value) || 1;

            priceInput.value = formatCurrency(price);
            subtotalSpan.textContent = formatCurrency(price * quantity);

            quantityInput.setAttribute('max', stock);
            if (quantity > stock) {
                quantityInput.value = stock;
                alert(`Số lượng không được vượt quá ${stock}`);
            }

            calculateOrderTotal();
        }

        function calculateOrderTotal() {
            let total = 0;
            document.querySelectorAll('.order-product-item').forEach(item => {
                const subtotalText = item.querySelector('.order-item-subtotal').textContent;
                const subtotal = parseFloat(subtotalText.replace(/[^\d]/g, '')) || 0;
                total += subtotal;
            });

            const totalElement = document.getElementById('orderTotalAmount');
            if (totalElement) {
                totalElement.textContent = formatCurrency(total);
            }
        }

        if (document.getElementById('orderForm') || document.querySelector('.order-product-item')) {
            bindOrderEvents();
            calculateOrderTotal();
        }
    }
    if (document.getElementById('itemsTable')) {
        console.log('Initializing inventory form...');
        initInventoryForm();
    }

    if (document.getElementById('orderForm')) {
        console.log('Initializing order management...');
        initOrderManagement();
    }
});
