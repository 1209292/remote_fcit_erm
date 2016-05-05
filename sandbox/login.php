<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
    <h2>This is AJAX</h2>
	<div id="update"></div>
	</body>
    <script>
       // object of XMLHttpRequest();
            var req = new XMLHttpRequest();
       // open request
            req.open('GET', 'ajax.txt');
            req.onreadystatechange = function(){
                if(req.status == 200 && req.readyState == 4) {
                    document.getElementById("update").innerHTML = req.responseText;
                }
            };
      // send data
        req.send();
    </script>
</html>