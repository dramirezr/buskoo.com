jQuery.fn.reset = function () {
	$(this).each (function() { this.reset(); });
}

var leaveConfirmationFlag = true;

var isTouch = false;
//var isTouch = (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0));


/*if(isTouch){
	$(window).bind('beforeunload', function() {
		if(leaveConfirmationFlag){
			leaveConfirmationFlag = true;
		    if(/Firefox[\/\s](\d+)/.test(navigator.userAgent) && new Number(RegExp.$1) >= 4) {
		        if(confirm(leave_msg_ff)) {
		            history.go();
		        } else {
		            window.setTimeout(function() {
		                window.stop();
		            }, 1);
		        }
		    } else {
		        return leave_msg_ch;
		    }			
		}
	});	
}*/

var directionsService = new google.maps.DirectionsService();

var styles = [
	              {
	            	    "featureType": "poi",
	            	    "stylers": [
	            	      { "visibility": "off" }
	            	    ]
	            	  },{
	            	    "featureType": "transit",
	            	    "stylers": [
	            	      { "visibility": "on" }
	            	    ]
	            	  },{
	            	    "featureType": "landscape.man_made",
	            	    "stylers": [
	            	      { "visibility": "off" }
	            	    ]
	            	  }
	            	];
	
function mapInitialize(location) {

	if (location) {
     	myLatlng = currLatlng = new google.maps.LatLng(
				location.coords.latitude, location.coords.longitude);
	} else {
     	myLatlng = sysDefaultLocation;
    }

	if (userDefaultLocation) {
		myLatlng = userDefaultLocation;
	}

	var cookie_latlng = cookie('latlng');

	if (cookie_latlng) {
     	myLatlng = new google.maps.LatLng(cookie_latlng.lat, cookie_latlng.lng);
	}

	cookie('latlng', {
		'lat' : myLatlng.lat(),
		'lng' : myLatlng.lng()
	});
	
	directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
	
	var mapOptions = {
		zoom : map_zoom,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP,
		mapTypeControl : true,
		mapTypeControlOptions : {
			position : google.maps.ControlPosition.RIGHT_CENTER
		},
		panControl : false,
		scrollwheel : true,
		navigationControl : true,
		streetViewControl : false,
		zoomControlOptions : {
			style : google.maps.ZoomControlStyle.SMALL,
			position : google.maps.ControlPosition.RIGHT_CENTER
		},
		styles : styles
	};

	map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

	myLocation = new google.maps.Marker({
		position : myLatlng,
		map : map,
		title : currentPosLbl,
		icon : base_url + 'assets/images/myLocation.png',
		draggable : true
	});
	
    codeLatLng();
	
    /*if (location) {
		if (location.coords.accuracy < 200) {
			var myLocationAccuracy = new google.maps.Circle({
				map : map,
				radius : location.coords.accuracy,
				fillColor : "#5882FA",
				strokeColor : "#2E2EFE",
				strokeWeight : 1
			});
			myLocationAccuracy.bindTo('center', myLocation, 'position');
		} else {
			alert(accuracyErrorMsg);
		}
	}*/

    google.maps.event.addListener(myLocation, 'dragend', function(e) {
        changeLocation(e.latLng);
    });
    
    directionsDisplay.setMap(map);

/*
	 * google.maps.event.addListener(map, 'click', function(event) {
	 * placeMarker(event.latLng); });
	 */

}

function changeLocation(latLng) {
    myLatlng = latLng;
    map.setCenter(myLatlng);
    cookie('latlng', {
        'lat' : myLatlng.lat(),
        'lng' : myLatlng.lng()
    });
	
    codeLatLng();
}

/*function placeMarker(location) {
    var marker = new google.maps.Marker({
        position : location,
        icon : base_url + 'assets/images/myLocation.png',
        map : map
    });
    map.setCenter(location);
}
*/

function errorHandler(err) {
		alert(location_failed);
    return null;

}

function get_tyes_html_row(data) {
    var html = '<div class="row">';
    for ( var x = 0; x < data.length; x++) {
        html = html + '<div class="small-2 columns">'
        + '<label class="text-color-white" for="type_' + data[x].id
        + '"><input class="bz-type" type="checkbox" name="type_'
        + data[x].id + '" id="type_' + data[x].id + '" value="'
        + data[x].id + '">' + data[x].name + '</label>' + '</div>';
    }

    var diff = 6 - data.length;
    if (diff > 0) {
        for ( var x = 0; x < diff; x++) {
            html = html + '<div class="small-2 columns">&nbsp;</div>';
        }
    }

    html = html + '</div>';
    return html;
}

function pullUpWrapper() {
    $('#wrapper').css('margin-bottom', '-11em');
}
function pullDownWrapper() {
    $('#wrapper').css('margin-bottom', '-4em');
}

function set_map_location(elem) {
    var name = elem.attr('name');
    var lat = elem.attr('lat');
    var lng = elem.attr('lng');

    changeLocation(new google.maps.LatLng(lat, lng));

    // myLatlng = new google.maps.LatLng(lat, lng);
    myLocation.setPosition(myLatlng);
    map.setCenter(myLatlng);
    

    $(".user-locations").each(function(index) {
        $(this).text($(this).attr('name'));
        $(this).attr('current', '0');
    });

    elem.text(name + '*');
    elem.attr('current', '1');
}

function goToMyCurrentLocation(location){
	myLatlng = currLatlng = new google.maps.LatLng(
			location.coords.latitude, location.coords.longitude);
	changeLocation(myLatlng);
    
	myLocation.setPosition(myLatlng);
}

rad = function(x) {return x*Math.PI/180;}

distHaversine = function(p1, p2) {
	  var R = 6371; // earth's mean radius in km
	  var dLat  = rad(p2.lat() - p1.lat());
	  var dLong = rad(p2.lng() - p1.lng());

	  var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
	          Math.cos(rad(p1.lat())) * Math.cos(rad(p2.lat())) * Math.sin(dLong/2) * Math.sin(dLong/2);
	  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	  var d = R * c;

	  return d.toFixed(3);
}

$(document).ready(function() {
    $.cookie.json = true;
    $.cookie.expires = 1;
    
    mapInitialize(null);
    //go to user location.
    //navigator.geolocation.getCurrentPosition(goToMyCurrentLocation, errorHandler);

    $('#chlocation-go-current-location, #goto-my-current-location').click(function (e){
    	e.preventDefault();
    	Foundation.libs.dropdown.close($('#change-location-wrapper'));
    	navigator.geolocation.getCurrentPosition(goToMyCurrentLocation, errorHandler);
    });

   
    var center_user = true;    

    if(post){
        center_user = false;
        var y = new google.maps.LatLng(post.lat, post.lng);
        var kmdistance = distHaversine(myLatlng, y);

        //set_drop(post.lat, post.lng, kmdistance, post.name, post.id);
        set_directions(post.lat, post.lng, kmdistance, post.name, post.id, true, '', '');
        
        map.setZoom(16);
        map.setCenter(y);
    }
    
    if(uposts){
        map.setZoom(12);
        
        $.each( uposts, function( index, post ) {
            
            var y = new google.maps.LatLng(post.lat, post.lng);
            var kmdistance = distHaversine(myLatlng, y);
            //set_drop(post.lat, post.lng, kmdistance, post.name, post.id, true);
            set_directions(post.lat, post.lng, kmdistance, post.name, post.id, true, '', '');

        });
        
    }

    if (center_user)
        navigator.geolocation.getCurrentPosition(goToMyCurrentLocation, errorHandler);
      

    /*$('#adv-search').click(function(e) {
		e.preventDefault();

		if ($('#adv-search-block').is(':visible')) { // Must be Hide
			$('#visible-advsearch').hide();
			$('#hiden-advsearch').show();
			$('#adv-search-block').slideUp("slow");
		} else { // Must be shown
			$('#hiden-advsearch').hide();
			$('#visible-advsearch').show();
			$('#adv-search-block').slideDown("slow");
		}
	});*/
    
    
    
    $('#view-all-types').click(function(e) {
        e.preventDefault();

        if ($("#extra-types").text().length == 0) { // Only once
            $.ajax({
                type : "POST",
                url : lang + "/api/ajax_get_all_types",
                dataType : "json",
                cache : true,
                data : {
                    hms1 : $('input[name="hms1"]').val()
                }
            }).done(function(response) {
                if (response.length) {
                    var c = 1;
                    var data = new Array();
                    var html = '';
                    for ( var x = 0; x < response.length; x++) {
                        if (c <= 6) {
                            data.push(response[x]);
                            c++;
                        } else {
                            c = 1;
                            html = html + get_tyes_html_row(data);
                            data = new Array();
                        }
                    }

                    if (data.length) {
                        html = html + get_tyes_html_row(data);
                    }

                    $('#extra-types').append(html);
                    $('.bz-type').click(function(e) {
                        get_bz_by_type($(this).val());
                    });
					
                    pullUpWrapper();
                    $('#extra-types').show("slow");
                    $('#hiden-types-bar').hide();
                    $('#visible-types-bar').show();
                }
            });
			
        } else {

            if ($('#extra-types')
                .is(':visible')) { // Must
                // be
                // Hide
                $('#extra-types').hide("slow");
                pullDownWrapper();
                $('#hiden-types-bar').show();
                $('#visible-types-bar').hide();
            } else {
                pullUpWrapper();
                $('#extra-types').show("slow");
                $('#hiden-types-bar').hide();
                $('#visible-types-bar').show();
            }

        }

    });

    $('#login-action').click(function(e) {
        e.preventDefault();
        
        leaveConfirmationFlag = false;
        
        $.ajax({
            type : "POST",
            url : $('#login-form').attr('action'),
            dataType : "json",
            data : {
                email : $('input[name="email"]').val(),
                passwd : $('input[name="passwd"]').val(),
                hms1 : $('input[name="hms1"]').val()
            }
        }).done(function(response) {
            if (response.status == 'error') {
                $('#login-error-msg').html(response.msg);
                $('#login-error-wrapper').show();
            } else {
            	//window.location.href = '.';
            	window.location.reload(true);
            }
        });

    });

    $('.user-locations').click(function(e) {
        e.preventDefault();
        set_map_location($(this));
    });

    $('#add-location-action')
    .click(
        function(e) {
            e.preventDefault();

            var name = $(
                'input[name="location-name"]')
            .val();
            if (!name) {
                $('#add-location-error-msg').html(err_msg_missing_field);
                $('#add-location-error-wrapper')
                .show();
                return false;
            }

            var def = $(
                'input[name="location-def"]') 
                .is(':checked') ? '1' : '0';
            var lat = myLatlng.lat();
            var lng = myLatlng.lng();

            $
            .ajax(
            {
                type : "POST",
                url : $(
                    '#add-location-form')
                .attr(
                    'action'),
                dataType : "json",
                data : {
                    name : name,
                    lat : lat,
                    lng : lng,
                    def : def,
                    hms1 : $(
                        'input[name="hms1"]')
                    .val()
                }
            })
            .done(
                function(response) {
                    if (response.status == 'error') {
                        $(
                            '#add-location-error-msg')
                        .html(
                            response.msg);
                        $(
                            '#add-location-error-wrapper')
                        .show();
                        return false;
                    }

                    $('input[name="location-name"]').val('');
                    $('#add-location-form-wrapper').foundation('reveal', 'close');

                    $('#set-default-location, #delete-location').show();

                    $(".user-locations")
                    .each(
                        function(
                            index) {
                            $(
                                this)
                            .text(
                                $(
                                    this)
                                .attr(
                                    'name'));
                            $(
                                this)
                            .attr(
                                'current',
                                '0');
                        });

                    $(
                        '#saved-locations')
                    .append(
                        '<li><a href="javascript:void(0)" class="user-locations" lat="'
                        + lat
                        + '" lng="'
                        + lng
                        + '" name="'
                        + name
                        + '" current="1">'
                        + name
                        + '*</a></li>');

                    $('.user-locations')
                    .click(
                        function(
                            e) {
                            e
                            .preventDefault();
                            set_map_location($(this));
                        });

                });

        });

    $('#set-default-location').click(function(e) {
        e.preventDefault();

        var name = '';
        $(".user-locations").each(function(index) {
            if ($(this).attr('current') == '1')
                name = $(this).attr('name');
        });

        if (!name)
            return false;

        $.ajax({
            type : "POST",
            url : $(this).attr('href'),
            dataType : "json",
            data : {
                name : name,
                hms1 : $('input[name="hms1"]').val()
            }
        }).done(function(response) {
            return true;
        });
    });

    $('#delete-location').click(function(e) {
        e.preventDefault();
	
        if(!confirm(location_delete)) 
            return false;

        var name = '';
        $(".user-locations").each(function(index) {
            if ($(this).attr('current') == '1')
                name = $(this).attr('name');                
        });        
        
        if (!name)          
            return false;

        $.ajax({
            type : "POST",
            url : $(this).attr('href'),
            dataType : "json",
            data : {
                name : name,
                hms1 : $('input[name="hms1"]').val()
            }
        }).done(function(response) {

            if (response.status == 'error') {
                alert(response.msg);
                return false;
            }

            $(".user-locations").each(function(index) {
                if ($(this).attr('current') == '1')
                    $(this).hide();
            });

        });

    });

    $('#signup-action').click(function(e) {
        e.preventDefault();

        var hms1 = $('input[name="hms1"]').val();
        var name = $('input[name="user_name"]').val();
        var email = $('input[name="user_email"]').val();
        var passwd = $('input[name="user_passwd"]').val();
        var passwd2 = $('input[name="user_passwd2"]').val();

        if (!name || !email || !passwd) {
            $('#signin-error-msg').html(err_msg_missing_field_signin);
            $('#signin-error-wrapper').show();
            return false;
        }

        if (passwd != passwd2) {
            $('#signin-error-msg').html(err_msg_mismatch_pass);
            $('#signin-error-wrapper').show();
            return false;
        }

        // Email format validation
        var emailReg = /^[a-zA-Z0-9._-]+([+][a-zA-Z0-9._-]+){0,1}[@][a-zA-Z0-9._-]+[.][a-zA-Z]{2,6}$/;
        if (!emailReg.test(email)) {
            $('#signin-error-msg').html(err_msg_wrong_email_format);
            $('#signin-error-wrapper').show();
            return false;
        }
        
        $('#waiting').show();
        $('#signup-form').hide();
        
        $.ajax({
            type : "POST",
            url : $(
                '#signup-form')
            .attr(
                'action'),
            dataType : "json",
            data : {
                hms1 : hms1,
                name : name,
                email : email,
                passwd : passwd
            }
        }).done(function(response) {
        	if (response.status == 'error') {
        		$('#signin-error-msg').html(response.msg);
        		$('#signin-error-wrapper').show();
                $('#waiting').hide();
                $('#signup-form').show();
        		return false;
        	}
            
            $('#waiting').hide();
            $('#succesfull').show();
        });

    });

    $('#nav-menu-back').click(function(e) {
        map.setCenter(myLatlng);
    });

    $('#nav-menu-move').click(function(e) {
        changeLocation(map.getCenter());
        myLocation.setPosition(myLatlng);
    });

    $('.nav-location').click(function(e) {

        var lat = $(this).attr('lat');
        var lng = $(this).attr('lng');

        changeLocation(new google.maps.LatLng(lat, lng));
        myLocation.setPosition(myLatlng);
        map.setCenter(myLatlng);
        
        Foundation.libs.dropdown.close($('#change-location-wrapper'));
    });

    $('#chlocation-action').click(function(e){
        var address = $('input[name="new_addr"]').val();
        if(address){
            geocoder.geocode( {
                'address': address
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    changeLocation(results[0].geometry.location);
                    myLocation.setPosition(results[0].geometry.location);
                    $('input[name="new_addr"]').val('');
                } else {
                    alert(locationErrorMsg);
                }
            });			
        }
		
        Foundation.libs.dropdown.close($('#change-location-wrapper'));
    });

    $('#search-btn').click(function(e){
				
        var text = $('input[name="search-text"]').val();
		
        if(!text)
            return false;
		
        $('input[name="search-start"]').val('0');
        $('input[name="search-pid"]').val('');
        search(true);
    });

    $('#search2-btn').click(function(e){
        e.preventDefault();
        console.log('Entrooo');
        $('#search-btn').trigger('click');
    });



					
    $('#close-panel-button, #open-panel-button').click(function(e){
        e.preventDefault();
        showHideLeftPanel();
    });
	
    /*$('.search-results-panel').click(function(e){
        e.preventDefault();

        var post_id = $(this).attr('id');
		
		
        if($('input[name="' + post_id + '-inmap"]').val() == '1'){
            $('input[name="' + post_id + '-inmap"]').val('0');
            $(this).attr('style', 'background: none repeat scroll 0 0 #FFFFFF !important;');
            removeMarker(post_id);
        } else {

            $('input[name="' + post_id + '-inmap"]').val('1');
			
            $(this).attr('style', 'background: none repeat scroll 0 0 #A1D1E0 !important;');
			
            var lat = $('input[name="' + post_id + '-lat"]').val();
            var lng = $('input[name="' + post_id + '-lng"]').val();

            var latLng = new google.maps.LatLng(lat, lng);
            addMarker(post_id, latLng);			
        }
		

    //console.log(markers);
    });*/
	
    $('#load-into-map').click(function(e){
        e.preventDefault();
    });
	
    $('.bz-type').click(function(e) {
        put_bz_by_type($(this).val());
    });
    
    $('#chpwd-action').click(function(e){
         e.preventDefault();
                  
        var hms1     = $('input[name="hms1"]').val();
        var oldpass  = $('input[name="chpwd_oldpasswd"]').val();
        var newpass  = $('input[name="chpwd_newpasswd"]').val();
        var newpasswd = $('input[name="chpwd_newpasswd2"]').val();
        var email = $('input[name="chpwd_email"]').val();

        if (!oldpass || !newpass || !newpasswd) {
            $('#chpwd-error-msg').html(chpwd_err_msg_missing_field);
            $('#chpwd-error-wrapper').show();
            return false;
        }

        if (newpass != newpasswd ) {
            $('#chpwd-error-msg').html(chpwd_error_keys_mistmatch);
            $('#chpwd-error-wrapper').show();
            return false;
        }
         
         $.ajax({
            type : "POST",
            url : $('#chpwd-form').attr('action'),        
            dataType : "json",
            data : {
                hms1     : hms1,
                email    : email,
                oldpass  : oldpass,
                newpass  : newpass,
                newpasswd : newpasswd
            }
        }).done(function(response){
            if(response.status == 'error'){
                $('#chpwd-error-msg').html(response.msg);
                $('#chpwd-error-wrapper').show();
                return false;
            }

            $('input[name="chpwd_oldpasswd"], input[name="chpwd_newpasswd"], input[name="chpwd_newpasswd2"]').val('');
            alert(response.msg);
            $('#chpwd-form-wrapper').foundation('reveal', 'close');
        }); 
    });
    
    
    $('#rateform-cancel-action').click(function(e){

    	$('#post_id').val('');
		$('#qf-post-name').html('');
		$('textarea[name="review"]').val('');
		$('#qualification option[value="1"]').attr('selected', true);

    	$('#add-qualification-wrapper').foundation('reveal', 'close');
    	$('#search-result-wrapper').foundation('reveal', 'open');
    });
    
    $('#rateform-action').click(function(e){
    	var hms1     = $('input[name="hms1"]').val();
    	var q = $('select[name="qualification"]').val();
    	var review = $('textarea[name="review"]').val();
    	var user_agent = $('input[name="user_agent"]').val();
    	var user_ip = $('input[name="user_ip"]').val();
    	var post_id = $('input[name="post_id"]').val();
    	var user_id = $('input[name="user_id"]').val();

        $.ajax({
            type : "POST",
            url : $('#qualification-form').attr('action'),        
            dataType : "json",
            data : {
                hms1 : hms1,
                post_id: post_id,
                q : q,
                review : review,
                user_agent : user_agent,
                user_ip : user_ip,
                user_id: user_id
            }
        }).done(function(response){
            if(response.status == 'error'){
                alert(response.msg);
                return false;
            }

        	$('#post_id').val('');
    		$('#qf-post-name').html('');
    		$('textarea[name="review"]').val('');
    		$('#qualification option[value="1"]').attr('selected', true);
            alert(response.msg);
        	$('#add-qualification-wrapper').foundation('reveal', 'close');
        	$('#search-result-wrapper').foundation('reveal', 'open');
        });    	
    });
    
    $('#back-results-btn').hide();
    $('#back-results-btn').click(function(e){
    	$('#search-result-wrapper').foundation('reveal', 'open');
    });
    
    $('.mybiz-link').click(function (e){
    	e.preventDefault();
    	
    	$('#biz-control-panel').foundation('reveal', 'open', {
    	    url: lang + '/api/open_business_panel',
    	    data: {post_id: $(this).attr('id')}
    	});
    	
    	$('#biz-control-panel').on('opened', function () {
      	  $(this).foundation('section', 'reflow');
      });
    });
    
    if(!isTouch){
    	$('input[name="search-text"]').focus();
    }
    
    $('input[name="search-text"]').keydown(function(e) {
        if(e.which == 13) {
        	e.preventDefault();
        	$('#search-btn').trigger('click');
        }
    });

	$('#login-close-modal').click(function (e){
		$('#login-form').reset();
		$('#recoverypass-form').reset();
		
		$('#login-form').show();
		$('#recoverypass-form').hide();
	});

	$('#contact-close-modal').click(function (e){
		$('#contact-form').reset();
	});
	
	$('#singup-close-modal').click(function (e){
		$('#signup-form').reset();
	});

	$('#recoverypass-form').hide();
	
	$('#forgot-password').click(function(e){
		e.preventDefault();
		
		$('#login-form').hide();
		$('#recoverypass-form').show();
	});
	
	$('#recoverypass-action').click(function(e){
		e.preventDefault();
		
    	var hms1     = $('input[name="hms1"]').val();
    	var email = $('input[name="recovery-email"]').val();
    	
    	if(!email)
    		return false;
    	
        $.ajax({
            type : "POST",
            url : $('#recoverypass-form').attr('action'),        
            dataType : "json",
            data : {
                hms1 : hms1,
                email: email
            }
        }).done(function(response){
            if(response.state == 'error'){
                alert(response.msg);
                return false;
            }
    		$('#login-form').show();
    		$('#recoverypass-form').hide();
    		$('#recoverypass-form').reset();
            alert(response.msg);
        });
    	
	});
	
	$('#toggle-menu').click(function(e){
		if($('#open-m').is(":visible")){
			$('#open-m').hide();
			$('#close-m').show();			
		}else{
			$('#close-m').hide();
			$('#open-m').show();
		}
	});
	
	$('#contactform-action').click(function(e){
		e.preventDefault();
		
    	var hms1 = $('input[name="hms1"]').val();
    	var email = $('input[name="ct-email"]').val();
		var subject = $('select[name="ct-subject"]').val();
    	var bzid = $('input[name="ct-bzid"]').val();
    	var msg = $('#ct-msg').val();
		
    	if(!email || !msg || !subject){
    		alert(ct_form_err_msg_missing_field);
    		return false;
    	}
    	
    	if((subject == 'Reportar local' || subject == 'Business issue') && !bzid){
    		alert(ct_form_err_msg_missing_bz_id);
    		return false;    		
    	}
    	
        $.ajax({
            type : "POST",
            url : $('#contact-form').attr('action'),        
            dataType : "json",
            data : {
                hms1 : hms1,
                email: email,
                subject : subject,
                msg : msg,
                bzid : bzid
            }
        }).done(function(response){
            if(response.state == 'error'){
                alert(response.msg);
                return false;
            }
            $('#contact-close-modal').trigger('click');
            alert(ct_form_err_okmsg);
        });
    	
	});    	



});



function change_sort(orderby){
	//$('#sort option').attr('selected', false);
	//$('#sort option[value="' + orderby + '"]').attr('selected', true);
	$('input[name="search-start"]').val(0);
	search(false, orderby);
}

var infoWindows = new Object();
var markers = new Object();
var icono_post = null;

function set_directions(lat, lng, distance, bz_name, post_id, drop_only, phones, address){

	var destination = new google.maps.LatLng(lat, lng);

	if(!drop_only){
		routeToHere(destination, distance);
	}
	
	num = new Number(distance) ;


	if(drop_only){
		map.setZoom(16);
		map.setCenter(destination);
	}
	

    var extrainfo;
    var email;
    var website;
    var fbpage;
    var icono;
    var logo;
    
    $.ajax({
            type : "GET",
            url : lang + '/api/show_info',        
            dataType : "json",
            data : {
                post_id : post_id
            }
        }).done(function(response){
            icono_post = '';
            if(response.state == 'ok'){
                extrainfo    = response.extrainfo ;
                email        = response.email ;
                website      = response.website ;
                fbpage       = response.fbpage ;
                icono        = response.icono_post ;
                logo         = '' ;
                if(response.logo!=''){
                    logo = media_server_show_url+'/'+response.logo ;
                    logo = '<img src="'+ logo +'"  width="80" height="80">';
                }

                if(response.icono_post[0].icon_biz!=null)
                    icono_post = response.icono_post[0].icon_biz;
                if(response.icono_post[0].icon_post!=null)
                    icono_post = response.icono_post[0].icon_post;

                //console.info('icono_post :'+icono_post);   
            }


            addMarker(post_id, destination);
        
            markers[post_id].setTitle(bz_name.toUpperCase() + ' (' + num.toFixed(2) + ' km)');
    
            google.maps.event.addListener(markers[post_id], 'click', function(){
            if(!isTouch){
                isTouch = new google.maps.InfoWindow();
            }
            var note =  '<div class="panel radius">' + 
                        '<h5>' + bz_name + '</h5>' +
                        '<div>'+ logo +'</div>' +
                        '<h6>' + extrainfo + '</h6>' +
                        '<h6>' + address + '</h6>' +
                        '<h6>' + phones + '</h6>' +
                        '<h6>' + email + '</h6>' +
                        '<h6>' + website + '</h6>' +
                        '<h6>' + fbpage + '</h6>' +
            
                        '<small>' + num.toPrecision(2) + ' km</small>' +   
                        '<a href="www.google.com" id="search2-btn" class="button alert small radius">Ver mas</a>' +
                        '<input type="hidden" name="search2-text" id="txtsearchtemp" value="' + bz_name + '"/>' +  
                        '</div>';
           
        
            isTouch.setContent(note);
            //isTouch.open(map, this);
           
            //siempre buscar en toda la base de datos y dejarlo igual
            var ckdistance = $("#ckdistance").is(':checked') ? true : false;
            document.getElementById('ckdistance').checked=true;
            $('input[name="search-pid"]').val(post_id);
            search(true);
            $('input[name="search-pid"]').val('');
            //$('#search-btn').trigger('click');
            document.getElementById('ckdistance').checked=ckdistance;
            //dfr
            // routeToHere(destination, distance);
        }); 


        google.maps.event.addListener(markers[post_id], 'dblclick', function(){  
            markers[post_id].setMap(null);
            markers[post_id] = null;
        }); 


    });
    
	
      
}

function set_drop(lat, lng, distance, bz_name, post_id, multiple){
	var destination = new google.maps.LatLng(lat, lng);
	
	num = new Number(distance);

	/*if(!isTouch){
		var contentString = '<div class="panel radius">' + 
		'<h5>' + bz_name + '</h5>' +
	//	'<small><a href="">' + msg1 + '</a></small><br/>' +		
		'<small>' + num.toPrecision(2) + 'km</small>' +		
		'</div>';
		
		infoWindows[post_id] = new google.maps.InfoWindow({
			content: contentString
		});		
	}*/
	
	if(!multiple){
		map.setZoom(16);
		map.setCenter(destination);	
    }
	
	addMarker(post_id, destination);
	markers[post_id].setTitle(bz_name + ' (' + num.toPrecision(2) + 'km)');
	google.maps.event.addListener(markers[post_id], 'click', function() {
		/*if(!isTouch){
			closeInfoWindows();
			infoWindows[post_id].open(map, markers[post_id]);
		}
		routeToHere(destination, distance);
		*/
		
		$('input[name="search-pid"]').val(post_id);
		search(true);
	});
}

function closeInfoWindows(){
	$.each( infoWindows, function( post_id, win ) {
		win.close();
	});
}

function routeToHere(destination, distance){
	
	var travelMode = google.maps.TravelMode.DRIVING;
	
	if(distance <= 1)
		travelMode = google.maps.TravelMode.WALKING;
		
	var request = {
		      origin: myLatlng,
		      destination: destination,
		      travelMode: travelMode
	};
	
	directionsService.route(request, function(result, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			directionsDisplay.setDirections(result);
		}else{
			alert("Sorry, We couldn't get the directions.");
		}
	});	
}

function search(openModal, orderby){
	
	//removeMarkers();
	directionsDisplay.set('directions', null);
	
	orderby = orderby ? orderby : $('#sort').val();
	var exactMatch = $("#exact-match").is(':checked') ? 1 : 0;
	var ckdistance = $("#ckdistance").is(':checked') ? true : false;
    var kmts = ckdistance ? 50000 : $('#distance').val();
    
	$.ajax({
        type : "POST",
        url : $('#search-form').attr('action'),
        dataType : "html",
        data : {
            text : $('input[name="search-text"]').val(),
            pid  : $('input[name="search-pid"]').val(),
            distance : kmts,
            rows : $('#rows').val(),
            start: $('input[name="search-start"]').val(),
            sort: orderby,
            post_type : $('#post_type').val(),
    		lat : myLatlng.lat(),
    		lng : myLatlng.lng(),
    		exact_match : exactMatch,
            hms1 : $('input[name="hms1"]').val()
        }
    }).done(function(response) {
        showSearchResults(response);
        if(openModal){
            $('#search-result-wrapper').foundation('reveal', 'open');
            $('#search-result-wrapper').on('opened', function () {
                  $(this).foundation('section', 'reflow');
            });        	
        }else{
        	$('#search-result-wrapper').foundation('section', 'reflow');
        }
        
    });
    
	$('#back-results-btn').show();
}

function removeMarkers(){
	$.each( markers, function( post_id, mark ) {
		removeMarker(post_id);
	});
	
	markers = new Object();
}

function removeMarker(id){
    markers[id].setMap(null);
    markers[id] = null;
}

function addMarker(id, latLng){
    markers[id] = placeMarker(latLng);
}

function placeMarker(location) {
   //console.log('Ruta:'+admin_server_icon_url+icono_post);
    if (icono_post != null){
        var marker = new google.maps.Marker({
            position : location,
            icon : admin_server_icon_url+icono_post,
            map : map
        });
    }else{
        var marker = new google.maps.Marker({
            position : location,
            map : map
        });  
    }
    //map.setCenter(location);
	
    return marker;
}

function showSearchResults(response){

    if (response.status == 'error') {
        alert(response.msg);
        return false;
    }
	
    $('#search-result-wrapper').html(response);
}

function showHideLeftPanel(){
	
    if ($('#left-panel').attr('class') == 'large-3 columns') {
		
        $('#left-panel').attr('class', 'large-1 columns');
        $('#right-panel').attr('class', 'large-11 columns');
		
        $('#close-panel-button').attr('style', 'display: none !important;');		
        $('#open-panel-button').attr('style', '');
		
        $('#clear-button-wrapper').hide();
		
    } else {
        $('#left-panel').attr('class', 'large-3 columns');
        $('#right-panel').attr('class', 'large-9 columns');

        $('#close-panel-button').attr('style', '');	
        $('#open-panel-button').attr('style', 'display: none !important;');

        $('#clear-button-wrapper').show();
    }
	
}

function codeLatLng() {
    geocoder.geocode({
        'latLng' : myLatlng
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                //map.setZoom(11);
                //infowindow.setContent(results[1].formatted_address);
                //infowindow.open(map, myLocation);
                $('#current-address').html(results[1].formatted_address);
            }
        } else {
            alert("Geocoder failed due to: " + status);
        }
    });
}

function cookie(name, val) {

    if (val) {
        return $.cookie(name, val);
    } else {
        return $.cookie(name);
    }
}


function bzCreationMapInit(map_wrapper_id, lat, lng) {
	
	var map_center = myLatlng;
	if(lat && lng){
		map_center = new google.maps.LatLng(lat, lng);
	}
	
	var mapOpt = {
		zoom : map.getZoom(),
		center : map_center,
		mapTypeId : google.maps.MapTypeId.ROADMAP,
		mapTypeControl : false,
		mapTypeControlOptions : {
			position : google.maps.ControlPosition.RIGHT_CENTER
		},
		panControl : false,
		scrollwheel : true,
		navigationControl : true,
		streetViewControl : false,
		zoomControlOptions : {
			style : google.maps.ZoomControlStyle.SMALL,
			position : google.maps.ControlPosition.RIGHT_CENTER
		},
		styles : styles
	};
 	
	
 	addbz_map = new google.maps.Map(document.getElementById(map_wrapper_id), mapOpt);

	addbz_marker = new google.maps.Marker({
		position : map_center,
		map : addbz_map
	});
	console.log('....bzCreationMapInit');
	google.maps.event.addListener(addbz_map, 'click', function(event) {
		if(addbz_marker)
			addbz_marker.setMap(null);
			
		addbz_marker = new google.maps.Marker({
			position : event.latLng,
			map : addbz_map
		});
		
		$('#bz-lat').val(event.latLng.lat());
		$('#bz-lng').val(event.latLng.lng());

	});
}

function keysForPhones(e) {
	var val = e.which;
	//console.log(e.which);
	if(val == 45 || val == 8 || val == 0 || (val >= 48 && val <= 57))
		return true;
		
	e.preventDefault();
}

function count_chars(elem, output, max){
	var text = elem.val()
	var remaining = max - text.length
	
	output.html(remaining)
}

function trunkEmails ( text ){
	var emailRegex = /([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi;
	return text.replace(emailRegex, '');
}

function trunkUrls(text){
	var urlRegex = /((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/;
	return text.replace(urlRegex, '');
}

function trunkMoreThan5NumbersTogether(text){
	var urlRegex = /[0-9]{6}/;
	return text.replace(urlRegex, '');	
}

function trunkMoreThan1BlankSpace(text){
	var Regex = /(?:\s\s+|\n|\t)/;
	return text.replace(Regex, ' ');	
}

function word_count(field) {

    var number = 0;
    var matches = field.val().match(/\b/g);
    if(matches) {
        number = matches.length/2;
    }
    return number;
}


function isValidDate(txtDate){

	var dateParts = txtDate.split('-');

	if(dateParts.length != 3) {
	    return false;
	}

	var testDate = new Date(dateParts[0] + '/' + dateParts[1] + '/' + dateParts[2]);

	if(isNaN(testDate.getDate())) {
	    return false;
	}
	
	return true;
}
