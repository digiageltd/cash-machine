<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>{{ trans('cash_machine.cash.machine.header') }}</title>
</head>
<body class="bg-gray-200">
<div class="container flex mx-auto justify-center items-center h-screen">
    <div class="bg-white rounded-lg shadow-md px-6 py-4 w-[600px]">
        <h1 class="text-center text-3xl font-bold underline">
            {{ trans('cash_machine.cash.machine.header') }}
        </h1>
        @yield('content')
    </div>
</div>
<script>
    function formatExpiration(input) {
        let value = input.value;
        value = value.replace(/[^\d-]/g, ''); // Remove any non-digit or non-hyphen characters
        const parts = value.split('-');
        let formattedValue = '';

        if (parts.length > 0) {
            const year = parts[0].substring(0, 4);
            let month = parts.length > 1 ? parts[1].substring(0, 2) : '';
            month = month.replace(/^0+/, ''); // Remove leading zeros
            formattedValue = `${year}-${month}`;
        }

        input.value = formattedValue;
    }

    function formatAccountNumber(input) {
        let value = input.value;
        value = value.replace(/[^a-zA-Z0-9]/g, ''); // Remove any non-alphanumeric characters
        value = value.substring(0, 6); // Limit the input to a maximum of 6 characters
        input.value = value;
    }

    function formatDate(input) {
        let value = input.value;
        value = value.replace(/[^\d-]/g, ''); // Remove any non-digit or non-hyphen characters
        const parts = value.split('-');
        let formattedValue = '';

        if (parts.length > 0) {
            const year = parts[0].substring(0, 4);
            let month = parts.length > 1 ? parts[1].substring(0, 2) : '';
            let day = parts.length > 1 ? parts[2].substring(0, 2) : '';
            formattedValue = `${year}-${month}-${day}`;
        }

        input.value = formattedValue;
    }
</script>

</body>
</html>
