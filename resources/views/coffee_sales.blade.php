<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New ☕️ Sales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <form id="sale-form" class="flex items-center space-x-4">
                <div class="flex-1"  @if(isset($defaultProductId)) style="display:none" @endif>
                <x-label for="product" value="Select Product" />
                    <select id="product" class="block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="" disabled selected>Select a product</option>
                        <!-- Populate dropdown with products from the database -->
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"@if(isset($defaultProductId)) selected="selected"@endif>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 ml-1">
                <x-label for="quantity" value="Quantity" />
                <x-input type="number" id="quantity" name="quantity" required />
                </div>

                <div class="flex-1 ml-1">
                <x-label for="unit_cost" value="Unit Cost (£)" />
                <x-input type="number" id="unit_cost" name="unit_cost" step="0.01" required />
                </div>

                <div class="flex-1 ml-2">
                <x-label value="Selling Price" />
                    <p id="selling-price" class="text-lg font-medium text-blue-600">£0.00</p>
                </div>

                <div class="flex-none">
                <x-button id="record-sale" type="button">Record Sale</x-button>
                </div>
            </form>
        </div>

        <!-- Table to display recorded sales -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Previous Sales</h2>
            <table class="min-w-full divide-y divide-gray-200" id="sales-table">
                <thead class="bg-gray-50">
                    <tr>
                        @if(!isset($defaultProductId))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product </th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-center" id="sales-tbody">
                    <!-- Sales records will be dynamically inserted here -->
                </tbody>
            </table>
        </div>
    </div>
            </div>
        </div>
        <script>
            // Function to calculate the selling price
            function calculateSellingPrice() {
                let productId = $('#product').val();
                let quantity = $('#quantity').val();
                let unitCost = $('#unit_cost').val();
               
                if (productId && quantity > 0 && unitCost > 0) {
                    $.ajax({
                        url: `/sales/${productId}/calculate`,
                        type: 'POST',
                        data: {
                            quantity: quantity,
                            unit_cost: unitCost,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            // Display the selling price
                            $('#selling-price').text('£' + response.selling_price.toFixed(2));
                        },
                        error: function () {
                            alert('An error occurred while calculating the price');
                        }
                    });
                } else {
                    $('#selling-price').text('-');
                }
            }

            // Event listeners for input changes
            $('#quantity, #unit_cost, #product').on('input change', calculateSellingPrice);


        var table = $('#sales-table').DataTable({
        "processing":true,
        "serverSide": true, //Feature control DataTables' servermside processing mode.
        "bFilter" : true,
        "bLengthChange": false,
        "ordering"  : false,
        "iDisplayLength" : 10,
        "responsive"  :true,
        "ajax": {
        "url": '{{ route("sales.data") }}',
        "type": "GET",
        "dataType": "json",
        "dataSrc": function (jsonData) {
        return jsonData.data;
      }
    },
    //Set column definition initialisation properties.
    "columnDefs": [
    {
        "targets": [ 0 ], //first column / numbering column
        "orderable": false, //set not orderable
      },
      ],
    });

            // Handle record sale button click
            $('#record-sale').on('click', function () {
                let productId = $('#product').val();
                let quantity = $('#quantity').val();
                let unitCost = $('#unit_cost').val();
                let sellingPrice = $('#selling-price').text().replace('£', '');

                // Check if the selling price has been calculated
                if (sellingPrice === '-' || sellingPrice <= 0.00) {
                    alert('Please calculate the selling price before recording the sale.');
                    return;
                }

                // AJAX call to save the sale
                $.ajax({
                    url: `/sales/${productId}/record-sale`,
                    type: 'POST',
                    data: {
                        quantity: quantity,
                        unit_cost: unitCost,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        
                        table.ajax.reload(null, false); 
                        // Clear the form fields
                        $('#sale-form')[0].reset();
                        $('#selling-price').text('-');
                    },
                    error: function () {
                        alert('An error occurred while recording the sale.');
                    }
                });
            });

        </script>
</x-app-layout>
