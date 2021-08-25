<!DOCTYPE html>
<html>
<head>
  <title>POST</title>
  <style id=h></style>
  <script>
    a = window.location.hash.substring(1);
    if (a) {
      document.getElementById("h").innerHTML = "form { display: none; }";
    }
  </script>
</head>
<body>
  <form id=f action=//<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];?> method=post>
    <label for=p>Copy URI fragment after # here</label>
    <br>
    <input id=p name=p type=text>
    <br>
    <input type=submit value=POST>
  </form>

  <script>
    if (a) {
      document.getElementById("p").value = a;
      document.getElementById("f").submit();
    }
  </script>
</body>
</html>
