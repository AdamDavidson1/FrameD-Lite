<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>FrameD Lite Example</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-language" value="en" />
	<meta http-equiv="X-UA-Compatible" content="IE=8" />
    <meta http-equiv="imagetoolbar" content="no" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="MSSmartTagsPreventParsing" content="TRUE" />

	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
	 	google.load('jquery','1.4.2');
    </script>

	<script type="text/javascript">
	     $(document).ready(function(){
			var vars = <?php echo json_encode($data); ?>;
			$.post('index.php?format=data',vars,function(data){
				$('#data').html(data);
			});

			$.post('index.php?format=json',vars,function(data){
				$('#json').html('<pre>' + JSON.stringify(data) + '</pre>');
			});

			$.post('index.php?format=xml',vars,function(data){
				$('#xml').text(new XMLSerializer().serializeToString(data));
				$('#xml').html('<pre>' + $('#xml').html() + '</pre>');
			});
		 });
	</script>
</head>
<body>

This is an example page
<h2>This is an example of DATA dump return</h2>
<div id="data"></div>

<h2>This is an example of JSON return</h2>
<div id="json"></div>

<h2>This is an example of XML return</h2>
<div id="xml"></div>
</body>
</html>
