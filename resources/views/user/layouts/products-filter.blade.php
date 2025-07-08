<aside class="col-lg-3 col-md-4 mb-4">
    <div class="p-4 border rounded shadow-sm bg-white sticky-top" style="top: 20px;">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-pink">
                <i class="bi bi-funnel-fill me-2"></i>Lọc sản phẩm
            </h5>
        </div>

        <form method="GET" action="{{ $actionRoute }}" id="filterForm">

            <div class="mb-4">
                <h6 class="fw-semibold mb-3 d-flex align-items-center text-dark">
                    <i class="bi bi-grid-3x3-gap me-2 text-pink"></i>
                    Danh mục
                </h6>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="category" id="cat_all" value=""
                        {{ request('category') == '' ? 'checked' : '' }}>
                    <label class="form-check-label fw-medium" for="cat_all">Tất cả danh mục</label>
                </div>
                @if(isset($categories) && count($categories) > 0)
                    @foreach($categories as $category)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="category"
                                   id="cat_{{ $category['value'] }}" value="{{ $category['value'] }}"
                                {{ request('category') == $category['value'] ? 'checked' : '' }}>
                            <label class="form-check-label" for="cat_{{ $category['value'] }}">{{ $category['label'] }}</label>
                        </div>
                    @endforeach
                @endif
            </div>

            <hr class="my-4">

            <div class="mb-4">
                <h6 class="fw-semibold mb-3 d-flex align-items-center text-dark">
                    <i class="bi bi-award me-2 text-pink"></i>
                    Thương hiệu
                </h6>

                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="brand" id="brand_all" value=""
                        {{ request('brand') == '' ? 'checked' : '' }}>
                    <label class="form-check-label fw-medium" for="brand_all">Tất cả thương hiệu</label>
                </div>

                @if(isset($brands) && count($brands) > 8)
                    <div class="brand-search mb-3">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="brandSearch" placeholder="Tìm thương hiệu...">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                        </div>
                    </div>

                    <div class="popular-brands mb-3">
                        @foreach(array_slice($brands, 0, 6) as $brand)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand"
                                       id="brand_{{ $loop->iteration }}" value="{{ $brand['value'] }}"
                                    {{ request('brand') == $brand['value'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="brand_{{ $loop->iteration }}">{{ $brand['label'] }}</label>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-grid mb-2">
                        <button class="btn btn-outline-secondary btn-sm" type="button"
                                data-bs-toggle="collapse" data-bs-target="#moreBrands">
                            <i class="bi bi-chevron-down me-1"></i>Xem thêm ({{ count($brands) - 6 }})
                        </button>
                    </div>

                    <div class="collapse" id="moreBrands">
                        <div class="more-brands border-top pt-2" style="max-height: 200px; overflow-y: auto;">
                            @foreach(array_slice($brands, 6) as $brand)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="brand"
                                           id="brand_more_{{ $loop->iteration }}" value="{{ $brand['value'] }}"
                                        {{ request('brand') == $brand['value'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="brand_more_{{ $loop->iteration }}">{{ $brand['label'] }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    @if(isset($brands))
                        @foreach($brands as $brand)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="brand"
                                       id="brand_{{ $loop->iteration }}" value="{{ $brand['value'] }}"
                                    {{ request('brand') == $brand['value'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="brand_{{ $loop->iteration }}">{{ $brand['label'] }}</label>
                            </div>
                        @endforeach
                    @endif
                @endif
            </div>

            <hr class="my-4">

            <div class="mb-4">
                <h6 class="fw-semibold mb-3 d-flex align-items-center text-dark">
                    <i class="bi bi-currency-dollar me-2 text-pink"></i>
                    Khoảng giá
                </h6>

                <div class="mb-3">
                    @if(isset($priceRanges))
                        @foreach($priceRanges as $range)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="price_range"
                                       id="price_{{ $loop->iteration }}" value="{{ $range['value'] }}"
                                    {{ request('price_range') == $range['value'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="price_{{ $loop->iteration }}">{{ $range['label'] }}</label>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="border rounded p-3 bg-light">
                    <label class="form-label small text-muted mb-2 fw-medium">Hoặc tùy chỉnh:</label>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <input type="number" name="min_price" class="form-control form-control-sm text-center"
                               placeholder="Từ" min="0" step="10000" value="{{ request('min_price') }}" style="width: 80px;">
                        <span class="text-muted fw-bold">-</span>
                        <input type="number" name="max_price" class="form-control form-control-sm text-center"
                               placeholder="Đến" min="0" step="10000" value="{{ request('max_price') }}" style="width: 80px;">
                    </div>
                    <small class="text-muted">Đơn vị: VND</small>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-pink fw-semibold py-2 rounded-pill">
                    <i class="bi bi-search me-2"></i> Tìm sản phẩm
                </button>
            </div>
        </form>

        @if(request()->hasAny(['category', 'brand', 'price_range', 'min_price', 'max_price']))
            <div id="activeFilters" class="mt-3">
                <div class="small text-muted mb-2 fw-medium">Bộ lọc đang áp dụng:</div>
                @if(request('category') && isset($categories))
                    @php
                        $selectedCategory = collect($categories)->firstWhere('value', request('category'));
                    @endphp
                    @if($selectedCategory)
                        <span class="badge bg-pink me-1 mb-1 rounded-pill">
                            {{ $selectedCategory['label'] }}
                            <i class="bi bi-x ms-1 cursor-pointer" onclick="removeFilter('category')"></i>
                        </span>
                    @endif
                @endif
                @if(request('brand'))
                    <span class="badge bg-pink me-1 mb-1 rounded-pill">
                        {{ request('brand') }}
                        <i class="bi bi-x ms-1 cursor-pointer" onclick="removeFilter('brand')"></i>
                    </span>
                @endif
                @if(request('price_range') || (request('min_price') || request('max_price')))
                    <span class="badge bg-pink me-1 mb-1 rounded-pill">
                        @if(request('price_range') && isset($priceRanges))
                            @php
                                $selectedPrice = collect($priceRanges)->firstWhere('value', request('price_range'));
                            @endphp
                            {{ $selectedPrice ? $selectedPrice['label'] : request('price_range') }}
                        @else
                            {{ number_format(request('min_price', 0)) }}đ - {{ number_format(request('max_price', 999999999)) }}đ
                        @endif
                        <i class="bi bi-x ms-1 cursor-pointer" onclick="removeFilter('price')"></i>
                    </span>
                @endif
            </div>
        @endif
    </div>
</aside>

<script>
    const brandSearch = document.getElementById('brandSearch');
    if (brandSearch) {
        brandSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const brandLabels = document.querySelectorAll('.popular-brands .form-check-label, .more-brands .form-check-label');

            brandLabels.forEach(label => {
                const brandName = label.textContent.toLowerCase();
                const formCheck = label.closest('.form-check');

                if (brandName.includes(searchTerm)) {
                    formCheck.style.display = 'block';
                } else {
                    formCheck.style.display = 'none';
                }
            });
        });
    }

    function removeFilter(type) {
        const url = new URL(window.location);

        switch(type) {
            case 'category':
                url.searchParams.delete('category');
                break;
            case 'brand':
                url.searchParams.delete('brand');
                break;
            case 'price':
                url.searchParams.delete('price_range');
                url.searchParams.delete('min_price');
                url.searchParams.delete('max_price');
                break;
        }

        window.location.href = url.toString();
    }
</script>
