<?php
class Tg_View_Helper_TrackingGoogle
{
		
	function trackingGoogle () 
	{
		$trackingAccount = Tg_Config::get('tracking.google.account');
		$trackingEnabled = Tg_Config::get('tracking.google.enabled');
		
		if ($trackingEnabled && $trackingAccount)
		{
?>
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '<?php echo $trackingAccount?>']);
    _gaq.push(['_trackPageview']);

    (function() {
        var ga = document.createElement('script'); 
        ga.type = 'text/javascript'; 
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>
<?php 
		}
		
	}
}