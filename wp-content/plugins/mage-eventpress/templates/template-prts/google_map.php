<?php 
add_action('mep_event_map','ggmap');	
function ggmap(){
global $event_meta,$user_api;
	 if($event_meta['mep_sgm'][0]){ if($user_api){?>
		<div class="mep-gmap-sec">
			<div id="map" class='mep_google_map'></div>
		</div>
				    <script>
				      var map;
				      function initMap() {
				        map = new google.maps.Map(document.getElementById('map'), {
				          center: {lat: <?php echo $event_meta['latitude'][0]; ?>, lng: <?php echo $event_meta['longitude'][0]; ?>},
				          zoom: 17
				        });
				        marker = new google.maps.Marker({
				          map: map,
				          draggable: false,
				          animation: google.maps.Animation.DROP,
				          position: {lat: <?php echo $event_meta['latitude'][0]; ?>, lng: <?php echo $event_meta['longitude'][0]; ?>}
				        });
				        marker.addListener('click', toggleBounce);				        
				      }
				      function toggleBounce() {
				        if (marker.getAnimation() !== null) {
				          marker.setAnimation(null);
				        } else {
				          marker.setAnimation(google.maps.Animation.BOUNCE);
				        }
				      }
				    </script>
				  	<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $user_api; ?>&callback=initMap"
				    async defer></script>		
<?php } } }?>