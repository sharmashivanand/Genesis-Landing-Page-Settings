$j = jQuery.noConflict();
$j(document).ready( function(){
	
	if( $j( "#glps_global_settings" ).is( ':checked' ) ) {
		$j( "#glps-mobile-use-global" ).css( "display", "none" );
	}
	else {
		$j( "#glps-mobile-use-global" ).css( "display", "block" );
	}
	
	$j( "#glps_global_settings" ).change(function() {
		var ischecked = this.checked;
		if(ischecked) {
			$j( "#glps-mobile-use-global" ).css( "display", "none" );
		}
		else{
			$j( "#glps-mobile-use-global" ).css( "display", "block" );
		}
	});
	
});