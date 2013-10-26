	
	    <nav class="top-bar">
	      <ul class="title-area">
	      

	        <!-- Title Area -->
	        <li class="name">
	        	<h1 id="bsk-logo"><span class="font-white">buskoo</span><span class="font-red">.com</span></h1>
	        </li> 
	      
	        <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
	        <li class="toggle-topbar menu-icon"><a href="javascript:void(0)" id="toggle-menu"><span id="open-m"><?=lang('dashboard.navmenu.open')?></span><span id="close-m" style="display: none;"><?=lang('dashboard.navmenu.close')?></span></a></li>
	      </ul>
		<!-- Left Nav Section -->
		
		<!-- END Left Nav Section -->
	      <section class="top-bar-section">
	        <ul class="left">
			  <? if($user): ?>
	          <li class="divider"></li>
	          <li class="has-dropdown"><a href="javascript:void(0)"><?=lang('dashboard.myaccount')?></a>
	          	 <ul class="dropdown">
                    <li><a href="javascript:void(0)" id="chpwd" data-reveal-id="chpwd-form-wrapper"><?=lang('dashboard.chpwd')?></a></li>

	          	 	<li class="has-dropdown"><a href="javascript:void(0)"><?=lang('dashboard.mylocations')?></a>
	          	 		<ul class="dropdown" id="saved-locations">
	          	 			<li><a href="#" data-reveal-id="add-location-form-wrapper"><?=lang('dashboard.addnewlocation')?></a></li>
	          	 			<li><a href="<?=base_url($this->lang->lang().'/api/delete_location')?>" <?= !isset($user_locations) ? 'style="display:none"' : ''?> id="delete-location"><?=lang('dashboard.deletelocation')?></a></li>
	          	 		<? if(isset($user_locations)): ?>
	          	 			<li class="divider"></li>
	          	 			<? foreach($user_locations as $l): ?>
	          	 			<? $name = ($l->def == '1') ?  $l->name.'*' : $l->name ?>
	          	 			<li><a href="javascript:void(0)" class="user-locations" lat="<?= $l->lat ?>" lng="<?= $l->lng ?>" name="<?= $l->name ?>" current="<?= $l->def ?>"><?= $name; ?></a></li>
	          	 			<? endforeach; ?> 
	          	 		<? endif; ?>
	          	 		</ul>
	          	 	</li>
              	 </ul> 
	          </li>
	          
	         
	          <li class="divider"></li>
	          <li class="has-dropdown"><a href="javascript:void(0)"><?=lang('dashboard.mybizs')?></a>
	          	<ul class="dropdown">
	          		<li><a href="<?=base_url($this->lang->lang().'/account/create_enterprise_form')?>" data-reveal-id="add-enterprise-form" data-reveal-ajax="true"><?=lang('dashboard.addbuz')?></a></li>
	          		<? if(isset($businesses)): ?>
		          		<? foreach($businesses as $b): ?>
		          		<li><a href="javascript:void(0)" id="<?=$b->id?>" class="<?= ($b->state == 'A') ? 'mybiz-link' : 'inactive-href'?>"><?=$b->name?></a></li>
		          		<? endforeach; ?>
	          		<? endif; ?>
	          	</ul>
	          <li>
	          
			  <? endif; ?>
			  
			  <? if(!$user): ?>
	          <li class="divider"></li>
	          <li><a href="javascript:void(0)" data-reveal-id="signup-form-wrapper"><?=lang('dashboard.signup')?></a></li>
	          <? endif; ?>
	        
	           <li class="divider"></li>
	           <li><a href="javascript:void(0)" id="goto-my-current-location" style="line-height: 0 !important; padding: 10px 5px 6px 5px;" data-tooltip data-options="disable-for-touch:true" class="has-tip" title="<?=lang('dashboard.navmenu.tooltip.target')?>"><? got_to_my_current_location_logo()?></a></li>
	           <li><a href="javascript:void(0)" id="nav-menu-back" style="line-height: 0 !important; padding: 10px 5px 6px 5px;" data-tooltip class="has-tip" data-options="disable-for-touch:true" title="<?=lang('dashboard.navmenu.tooltip.whereami')?>"><? where_am_i_logo() ?></a></li>
	           <li><a href="javascript:void(0)" id="nav-menu-move" style="line-height: 0 !important; padding: 10px 5px 6px 5px;" data-tooltip class="has-tip" data-options="disable-for-touch:true" title="<?=lang('dashboard.navmenu.tooltip.placemehere')?>"><? place_me_here_logo() ?></a></li>
	           
	          <!--<li class="has-dropdown"><a href="javascript:void(0)"><?=lang('dashboard.navmenu')?></a>
	          	<ul class="dropdown">
	          		<li><a href="javascript:void(0)" id="nav-menu-back"><?=lang('dashboard.navmenu.back')?></a></li>
	          		<li><a href="javascript:void(0)" id="nav-menu-move"><?=lang('dashboard.navmenu.move')?></a></li>
	          	</ul>
	          </li>-->
	        </ul>
	      </section>
		 
		 <!-- Right Nav Section -->
	      <section class="top-bar-section">
	        <ul class="right">
	          
	        <? foreach($follow_us_links as $f): ?>
		    	<? 
		    		$name = ucfirst(str_replace('follow_us_', '', $f->keyname));
		    		$icon = get_config_val("{$name}_icon");
		    	?>
		        <li><a href="<?= $f->value; ?>" <?= ($icon) ? 'style="line-height: 0 !important; padding: 10px 5px 6px 0;"' : ''?> target="_blank"><?= ($icon) ? $icon : $name; ?></a></li>	
		    <? endforeach; ?>
		          	 	
		          	 			     
	          <li class="divider"></li>
	          <li class="has-dropdown"><a href="javascript:void(0)" id="about"><?=lang('dashboard.about')?></a>
	         	 <ul class="dropdown">	         	 	
	         	 	<li><a href="javascript:void(0)" data-reveal-id="contact-modal"><?=lang('dashboard.contactus')?></a></li>
	         	 	<li><a href="<?=base_url($this->lang->lang().'/terms')?>" data-reveal-id="terms-modal" data-reveal-ajax="true"><?=lang('dashboard.terms')?></a></li>
	         	 </ul>
	          </li>
			
       	<?
       		if($this->lang->lang() == 'en'){
       			$other_lang = 'es';
       			$other_lang_txt = 'Español';            			
       		}else{
       			$other_lang = 'en';
       			$other_lang_txt = 'English';            			
       		}
       	?>			
	          <li class="divider"></li>
	          <li><a href="<?=base_url($other_lang)?>" id="about"><?=$other_lang_txt?></a>
	          
	          </li>
			
			<? if($user): ?>
	          <li class="divider"></li>
	          <li><?=anchor('account/logout', lang('dashboard.logout') )?></li>
	        <? endif; ?>
	             
	        </ul>
	      </section>
	    </nav>

	<!-- Terms modal -->
	<div class="reveal-modal expand" id="terms-modal"></div>
	<!-- End Terms modal -->

	<!-- Contact modal -->
	<div class="reveal-modal small" id="contact-modal">
		<?= form_open('api/contact', array('id' => 'contact-form', 'class' => '')) ?>
			<h4><?=lang('dashboard.contactform.title')?></h4>

			<div class="row">
				<div class="large-12 columns">
					<p><small><?=lang('dashboard.contactform.help')?></small></p>
			    </div>
    		</div>
						
			<div class="row">
				<div class="large-12 columns">
			    	<label><?=lang('dashboard.loginform.username')?>*</label>
			        <input type="text" name="ct-email" id="ct-email"/>
			    </div>
    		</div>
    		<?php
    			$lang = $this->lang->lang() ;
    			$contact_subjects = get_config_val("comunication_types_$lang");
    			$contact_subjects = explode(',', $contact_subjects);
    		?>
			<div class="row">
				<div class="large-12 columns">
			    	<label><?=lang('dashboard.contactform.subject')?>*</label>
					<select id="ct-subject" name="ct-subject" class="medium">
						<? foreach($contact_subjects as $cs): ?>
						<option value="<?=$cs?>"><?=$cs?></option>
						<? endforeach; ?>
					</select>			        
			    </div>
    		</div>

			<div class="row">
				<div class="large-12 columns">
			    	<label><?=lang('dashboard.contactform.bzid')?> (<small><?=lang('dashboard.contactform.bzid-help')?></small>)</label>
			        <input type="text" name="ct-bzid" id="ct-bzid"/>
			    </div>
    		</div>

			<div class="row">
				<div class="large-12 columns">
			    	<label><?=lang('dashboard.contactform.msg')?>* (<span id="chcount-ct-msg"></span>)</label> 
			        <textarea maxlength="250" name="ct-msg" id="ct-msg"></textarea>
			        
			    </div>
    		</div>

			<div class="row">
				<div class="large-12 columns"><a href="javascript:void(0)" id="contactform-action" class="button"><?=lang('dashboard.contactform.button')?></a></div>
    		</div>
		</form>
		<script>
			var ct_max_chars = 250;
			var ct_form_err_msg_missing_field = '<?=lang('dashboard.contactform.errmsg-misfield')?>';
			var ct_form_err_msg_missing_bz_id = '<?=lang('dashboard.contactform.errmsg-misbzid')?>';
			var ct_form_err_okmsg = '<?=lang('dashboard.contactform.okmsg')?>';
			
			$(document).ready(function(){
				count_chars($('#ct-msg'), $('#chcount-ct-msg'), ct_max_chars);
				$('#ct-msg').keyup(function(event) {
					count_chars($(this), $('#chcount-ct-msg'), ct_max_chars);
				});	
			});
		</script>
		<a class="close-reveal-modal" id="contact-close-modal">&#215;</a>
	</div>
	<!-- End Contact modal -->

	<? if($user): ?>
	<!-- Add Location Form -->
	<div class="reveal-modal small" id="add-location-form-wrapper">
		<?= form_open('api/add_location', array('id' => 'add-location-form', 'class' => '')) ?>
			<h4><?=lang('dashboard.locationform.title')?></h4>
			<p><?=lang('dashboard.locationform.help')?></p>
			
			<div class="row hide" id="add-location-error-wrapper">
				<div data-alert class="alert-box alert">
  					<span id="add-location-error-msg"></span>
				</div>
			</div>
			
			<div class="row">
				<div class="large-12 columns">
			    	<label><?=lang('dashboard.locationform.name')?></label>
			        <input type="text" name="location-name"/>
			    </div>
    		</div>

			
			<div class="row">
				<div class="large-12 columns"><a href="javascript:void(0)" id="add-location-action" class="button"><?=lang('dashboard.locationform.button')?></a></div>
    		</div>
		</form>
		<script>
			var err_msg_missing_field = '<?=lang('dashboard.locationform.errmsg')?>';
		</script>
		<a class="close-reveal-modal">&#215;</a>
	</div>	
	<!-- End Add Location Form-->
	
	<!-- Add Enterprise Form -->
	<div class="reveal-modal expand" id="add-enterprise-form"></div>
	<!-- End Add Enterprise Form -->

	<!-- Biz control panel Form -->
	<div class="reveal-modal expand" id="biz-control-panel"></div>
	<!-- End Biz control panel Form -->

	<!-- Change Password Form -->
	<div class="reveal-modal small" id="chpwd-form-wrapper">
            <?= form_open('account/change_password', array('id' => 'chpwd-form', 'class' => '')) ?>
            <input type="hidden" name="chpwd_email" value="<?=$user->email?>">
            <h5><?=lang('dashboard.chpwd')?></h5>
            <div class="row hide" id="chpwd-error-wrapper">
                <div data-alert class="alert-box alert">
                    <span id="chpwd-error-msg"></span>
                </div>
            </div>
            
            <div class="row">
		<div class="large-12 columns">
                   <label><?=lang('dashboard.chpwd.oldpass')?>*</label>
		   <input type="password" name="chpwd_oldpasswd"/>
		</div>
	    </div>
            <div class="row">
		<div class="large-12 columns">
		   <label><?=lang('dashboard.chpwd.newpass')?>*</label>
                    <input type="password" name="chpwd_newpasswd" />
		</div>
            </div>
            <div class="row">
		<div class="large-12 columns">
		   <label><?=lang('dashboard.chpwd.newpass2')?>*</label>
		   <input type="password" name="chpwd_newpasswd2" />
		 </div>
	   </div>
            <div class="row">
	        <div class="large-12 columns"><a href="javascript:void(0)" id="chpwd-action" class="small  button"><?=lang('dashboard.chpwd.button')?></a></div>
    	    </div>
          </form>
            <a class="close-reveal-modal">&#215;</a>
            
            <script>
                var chpwd_err_msg_missing_field = "<?=lang('dashboard.signupform.errmsg')?>";
                var chpwd_error_keys_mistmatch = "<?=lang('dashboard.signupform.errmsg.pass')?>";
                var location_delete = "<?=lang('dashboard.location.delete')?>";
                
            </script>
        </div>
	<!-- End Change Password Form -->

	<? endif; ?>
	