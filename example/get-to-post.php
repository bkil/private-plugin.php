<!DOCTYPE html>
<html>
<head>
  <title>POST</title>
  <link rel="shortcut icon" type=image/x-icon href=data:image/x-icon;,>
  <style id=h></style>
  <script>
    a = window.location.hash.substring(1);
    if (a) {
      document.getElementById("h").innerHTML = "form { display: none; }";
    }
  </script>
</head>
<body>
  <form action=. method=post>
    <label for=p>Copy URI fragment after # here</label>
    <br>
    <input id=p name=p type=text>
    <br>
    <input type=submit value=POST>
  </form>

  <script>
    if (a) {
      document.getElementById("p").value = a;
      document.forms[0].submit();
    }
  </script>
</body>
</html>
