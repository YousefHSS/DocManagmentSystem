<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Styled HTML Email</title>
    <style>
        /* Reset styles for email */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        /* Main content styles */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
        }

        /* Button styles */
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #000000;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        /* Responsive styles */
        @media screen and (max-width: 600px) {
            .container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
@php
    $a = $action ?? "none";
@endphp
<div class="container">
    <h1>A Document has been {{ $a }}</h1>
        <p>Document has been {{$a == 'approved' ? $a : $a.' because '.$reason}} </p>
    <p>To check the document please click on the below link.</p>
    <a href="{{ $link?? '#' }}" class="button fw-bolder"
       style="-webkit-text-stroke: 0.3px black;">Click Here</a>
</div>
</body>
</html>
