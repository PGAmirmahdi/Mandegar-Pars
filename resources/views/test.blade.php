<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test</title>
</head>
<body>
<h1>Test Echo</h1>
<script src="{{ mix('/js/app.js') }}"></script>
<script>
    console.log(window.Echo); // Check if Echo is defined
    Echo.channel('test-channel')
        .listen('TestEvent', (e) => {
            console.log('Event data:', e);
        });
</script>
</body>
</html>
