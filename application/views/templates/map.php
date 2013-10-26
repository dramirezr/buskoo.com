<div class="row full-width" style="max-height: inherit; height: 100%; margin: 0 !important">
	
	<div id="map_canvas" class="row full-width"></div> 
	
<?/*hide-for-small*/?>
<div class="row full-width footer hide">
	<div class="small-12 large-centered columns">
		<?= form_open() ?>
		
		<div class="panel callout opacity07 text-color-white padding-10px">
			<div class="row">
			</div>
				
			<div class="hide" id="extra-types"></div>
								
		</div>
		
		</form>
	</div>
</div>
	
</div>



<script>
	var map = new Object();
	var myLocation = new Object();
	var myLatlng = new Object();
	var markers = new Object();
	
	var geocoder = new google.maps.Geocoder();
	var infowindow = new google.maps.InfoWindow();
	var directionsDisplay;
	
	var sysDefaultLocation = new google.maps.LatLng('<?= $system_location[0] ?>', '<?= $system_location[1] ?>');
	<? if(isset($userDefaultLocation)): ?>
	var userDefaultLocation = new google.maps.LatLng('<?= $userDefaultLocation[0] ?>', '<?= $userDefaultLocation[1] ?>');	
	<? else: ?>
	var userDefaultLocation = null;		
	<? endif; ?>
	var map_zoom = <?= $map_zoom ?>;
	
	var currentPosLbl = '<?=lang('dashboard.searchform.currlocation')?>';
	var accuracyErrorMsg = '<?=lang('dashboard.searchform.accuracyerror')?>';	
	var locationErrorMsg = "<?=lang('dashboard.searchform.locationerror')?>";
	var location_failed = "<?=lang('dashboard.location.failed')?>";
	
	var post = <?= isset($post) ? $post : 'null' ?>;
	var uposts = <?= isset($uposts) ? $uposts : 'null' ?>;
	
</script>




