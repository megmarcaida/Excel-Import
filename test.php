<html>
<head>
    <title>Form Validation</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</head>
<body>
<form action="submitForm.php" action="post" onsubmit="return MyValidation()">
    <input id="name" type="text" name="name" />
    <input id="sample"type="text" name="sample" />
    <input id="test" type="text" name="test" />
    <input type="submit" name="submit" />
</form>
</body>
<script>
    function MyValidation() {
        var valid = false;

        $.ajax({
            type: "POST",
            url: "validation.php",
            async: false,
            data: { name: $('#name').val(), sample : $('#sample').val(), test : $('#test') }
        })
            .done(function( data ) {
                if(data == 'true') {
                    valid = true;
                }
            });

        // not valid, return false and show some hidden message
        return valid;
    }
</script>
</html>