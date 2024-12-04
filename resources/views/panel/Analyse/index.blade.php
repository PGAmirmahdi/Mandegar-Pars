@extends('panel.layouts.master')

@section('content')
    <div class="container">
        <form id="stepForm">
            @csrf

            <!-- مرحله 1: انتخاب تاریخ -->
            <div class="step" id="step-1">
                <h4>مرحله 1: انتخاب تاریخ</h4>
                <div class="form-group">
                    <label for="date">تاریخ</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                <button type="button" class="btn btn-primary next-btn">بعدی</button>
            </div>

            <!-- مرحله 2: انتخاب یا اضافه کردن دسته‌بندی -->
            <div class="step" id="step-2" style="display: none;">
                <h4>مرحله 2: دسته‌بندی</h4>
                <div class="form-group">
                    <label for="category">دسته‌بندی</label>
                    <select id="category" name="category" class="form-control" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="add-category" class="btn btn-secondary mt-2">افزودن دسته‌بندی</button>
                </div>
                <button type="button" class="btn btn-secondary prev-btn">قبلی</button>
                <button type="button" class="btn btn-primary next-btn">بعدی</button>
            </div>

            <!-- مرحله 3: انتخاب یا اضافه کردن مدل -->
            <div class="step" id="step-3" style="display: none;">
                <h4>مرحله 3: مدل</h4>
                <div class="form-group">
                    <label for="model">مدل</label>
                    <select id="model" name="model" class="form-control" required>
                        <option value="">ابتدا دسته‌بندی را انتخاب کنید</option>
                    </select>
                    <button type="button" id="add-model" class="btn btn-secondary mt-2">افزودن مدل</button>
                </div>
                <button type="button" class="btn btn-secondary prev-btn">قبلی</button>
                <button type="button" class="btn btn-primary next-btn">بعدی</button>
            </div>

            <!-- مرحله 4: جدول محصولات -->
            <div class="step" id="step-4" style="display: none;">
                <h4>مرحله 4: محصولات</h4>
                <table class="table" id="productsTable">
                    <thead>
                    <tr>
                        <th>نام محصول</th>
                        <th>کد</th>
                        <th>تعداد پیش‌فاکتور</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <button type="button" class="btn btn-secondary prev-btn">قبلی</button>
                <button type="submit" class="btn btn-success">ذخیره</button>
            </div>
        </form>
    </div>

    <!-- مدال افزودن مدل -->
    <div class="modal" id="addModelModal" tabindex="-1" aria-labelledby="addModelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModelModalLabel">افزودن مدل جدید</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="newModelName">نام مدل</label>
                        <input type="text" id="newModelName" class="form-control" placeholder="نام مدل" required>
                    </div>
                    <div class="form-group mt-2">
                        <label for="category_id">دسته‌بندی</label>
                        <select id="category_id" class="form-control">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    <button type="button" id="saveModelButton" class="btn btn-primary">ذخیره</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentStep = 1;
            const totalSteps = 4;

            function showStep(step) {
                // پنهان کردن تمامی مراحل
                document.querySelectorAll('.step').forEach((stepDiv) => {
                    stepDiv.classList.remove('active');
                });

                // نمایش مرحله جاری
                document.querySelector(`#step-${step}`).classList.add('active');
            }

            // نمایش مرحله اول به طور پیش‌فرض
            showStep(currentStep);

            // مدیریت دکمه‌های بعدی و قبلی
            document.querySelectorAll('.next-btn').forEach(button => {
                button.addEventListener('click', function () {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        showStep(currentStep);
                    }
                });
            });

            document.querySelectorAll('.prev-btn').forEach(button => {
                button.addEventListener('click', function () {
                    if (currentStep > 1) {
                        currentStep--;
                        showStep(currentStep);
                    }
                });
            });
        });


        // نمایش مدال افزودن مدل
            document.getElementById('add-model').addEventListener('click', function () {
                var addModelModal = new bootstrap.Modal(document.getElementById('addModelModal'));
                addModelModal.show();
            });

            // ذخیره مدل جدید
            document.getElementById('saveModelButton').addEventListener('click', function () {
                var modelName = document.getElementById('newModelName').value;
                var categoryId = document.getElementById('category_id').value;

                if (modelName) {
                    // ارسال درخواست به سرور برای ذخیره مدل جدید
                    fetch('{{ route('add.storeModel') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            name: modelName,
                            category_id: categoryId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            // پس از ذخیره مدل جدید، مدل جدید به لیست اضافه می‌شود
                            var modelSelect = document.getElementById('model');
                            var option = document.createElement('option');
                            option.value = data.id;
                            option.textContent = data.name;
                            modelSelect.appendChild(option);

                            // بستن مدال
                            var addModelModal = bootstrap.Modal.getInstance(document.getElementById('addModelModal'));
                            addModelModal.hide();

                            // خالی کردن فیلدها
                            document.getElementById('newModelName').value = '';
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                } else {
                    alert('لطفاً نام مدل را وارد کنید.');
                }
            });

            // انتخاب دسته‌بندی و بارگذاری مدل‌ها
            document.getElementById('category').addEventListener('change', function () {
                let categoryId = this.value;
                fetch('{{ route('get.models') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({category_id: categoryId})
                })
                    .then(response => response.json())
                    .then(data => {
                        let modelSelect = document.getElementById('model');
                        modelSelect.innerHTML = '';
                        data.forEach(model => {
                            let option = document.createElement('option');
                            option.value = model.id;
                            option.textContent = model.name;
                            modelSelect.appendChild(option);
                        });
                        modelSelect.disabled = false;
                    });
            });

            // انتخاب مدل و بارگذاری محصولات
            document.getElementById('model').addEventListener('change', function () {
                let modelId = this.value;
                let categoryId = document.getElementById('category').value;

                fetch('{{ route('get.products') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({category_id: categoryId, model_id: modelId})
                })
                    .then(response => response.json())
                    .then(data => {
                        let tableBody = document.querySelector('#productsTable tbody');
                        tableBody.innerHTML = '';
                        data.forEach(product => {
                            let row = document.createElement('tr');
                            row.innerHTML = `
                            <td>${product.title}</td>
                            <td>${product.code}</td>
                            <td><input type="number" name="products[${product.id}][quantity]" class="form-control" value="0"></td>
                        `;
                            tableBody.appendChild(row);
                        });
                    });
            });
    </script>
@endsection
