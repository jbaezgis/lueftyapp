<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Message</title>
</head>
<body>
    <p>Name: {{ $contact['fullname'] }}</p>
    <p>Email: {{ $contact['email'] }}</p>
    <p>Phone: {{ $contact['phone'] }}</p>
    <p>Message: {{ $contact['message'] }}</p>

</body>
</html>