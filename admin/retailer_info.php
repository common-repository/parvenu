<?php
if ( !defined( 'ABSPATH' ) ) exit;
$parvenu_retailer_info = get_option( 'parvenu_retailer_info' );
?>

<div class="retailer_info">
   <iframe width="560" height="315" src="https://www.youtube.com/embed/keQG_Au0MmA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen
        style="display: block; margin: 0 auto;"
        ></iframe>
    <p>
      "91% of shoppers will switch brands because one is associated with a good cause.” - National Retail Federation 2019<br>
      <br>
Parvenu helps stores raise more for charity and drive engagement through personalization. Your shoppers will be asked to donate $1 to a charity at checkout. We use artificial intelligence to predict which charity they are most likely to care about and show them that charity. You get to make impact as a brand differentiator, personalization to drive engagement, and collect all the tax deductions. It’s entirely free and we’ll take care of processing the donations and sending the money to the charities. Fill out your information below and we’ll take care of everything else!
<br>
If you’ve got any questions, feel free to email me at <a href="mailto:patrick@parveneunext.com">
patrick@parveneunext.com</a>
      
    </p>
    <form>
      <?php wp_nonce_field( 'parvenu_nonce', 'parv_update_retailer_info_nonce' ); ?>
      <div class="form-row">
        <div class="form-group col-md-4 no_pl">
          <label for="first_name"><?php _e( 'First Name' , 'parvenu' ); ?></label>
          <input type="text" name="first_name" class="form-control" id="first_name" placeholder="<?php echo (!empty($parvenu_retailer_info['first_name'])) ? htmlspecialchars($parvenu_retailer_info['first_name']) : '' ?>" value="<?php echo (!empty($parvenu_retailer_info['first_name'])) ? htmlspecialchars($parvenu_retailer_info['first_name']) : '' ?>">
        </div>
        <div class="form-group col-md-4">
          <label for="last_name"><?php _e( 'Last Name' , 'parvenu' ); ?></label>
          <input type="text" name="last_name" class="form-control" id="last_name" placeholder="<?php echo (!empty($parvenu_retailer_info['last_name'])) ? htmlspecialchars($parvenu_retailer_info['last_name']) : '' ?>" value="<?php echo (!empty($parvenu_retailer_info['last_name'])) ? htmlspecialchars($parvenu_retailer_info['last_name']) : '' ?>">
        </div>
        <div class="form-group col-md-4 no_pr">
            <label for="email"><?php _e( 'Email' , 'parvenu' ); ?></label>
            <input type="text" name="email" class="form-control" id="email" placeholder="<?php echo (!empty($parvenu_retailer_info['email'])) ? htmlspecialchars($parvenu_retailer_info['email']) : '' ?>" value="<?php echo (!empty($parvenu_retailer_info['email'])) ? htmlspecialchars($parvenu_retailer_info['email']) : '' ?>">
      </div>
      </div>
      <div class="form-group">
        <label for="address"><?php _e( 'Address' , 'parvenu' ); ?></label>
        <textarea name="address"class="form-control" id="address" rows="3"><?php echo (!empty($parvenu_retailer_info['address'])) ? htmlspecialchars($parvenu_retailer_info['address']) : '' ?></textarea>      
      </div>
      <div class="form-row">
        <div class="form-group col-md-6 no_pl">
          <label for="company">Company</label>
          <input type="text" name="company" class="form-control" id="company" placeholder="<?php echo (!empty($parvenu_retailer_info['company'])) ? htmlspecialchars($parvenu_retailer_info['company']) : '' ?>" value="<?php echo (!empty($parvenu_retailer_info['company'])) ? htmlspecialchars($parvenu_retailer_info['company']) : '' ?>">
        </div>
        <div class="form-group col-md-6 no_pr">
          <label for="city">City</label>
          <input type="text" name="city" class="form-control" id="city" placeholder="<?php echo (!empty($parvenu_retailer_info['city'])) ? htmlspecialchars($parvenu_retailer_info['city']) : '' ?>" value="<?php echo (!empty($parvenu_retailer_info['city'])) ? htmlspecialchars($parvenu_retailer_info['city']) : '' ?>">
        </div>
       </div>
        <div class="form-row">
        <div class="form-group col-md-6 no_pl">
          <label for="state">State</label>
          <input type="text" name="state" class="form-control" id="state" placeholder="<?php echo (!empty($parvenu_retailer_info['state'])) ? htmlspecialchars($parvenu_retailer_info['state']) : '' ?>" value="<?php echo (!empty($parvenu_retailer_info['state'])) ? htmlspecialchars($parvenu_retailer_info['state']) : '' ?>">
        </div>
        <div class="form-group col-md-6 no_pr">
          <label for="zip">Zip</label>
          <input type="text" name="zip" class="form-control" id="zip" placeholder="<?php echo (!empty($parvenu_retailer_info['zip'])) ? htmlspecialchars($parvenu_retailer_info['zip']) : '' ?>" value="<?php echo (!empty($parvenu_retailer_info['zip'])) ? htmlspecialchars($parvenu_retailer_info['zip']) : '' ?>">
        </div>
      </div>
    <div class="form-row">
        <div class="form-group col-md-6 no_pl">
          <label for="website">Website</label>
          <input type="text" name="website" class="form-control" id="website" placeholder="<?php echo (!empty($parvenu_retailer_info['website'])) ? htmlspecialchars($parvenu_retailer_info['website']) : '' ?>" value="<?php echo (!empty($parvenu_retailer_info['website'])) ? htmlspecialchars($parvenu_retailer_info['website']) : '' ?>">
        </div>
        
      </div>
    
      <p class="submit">
          <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"><span class="submit_preloader" style="margin-left:20px;"><img src="<?php echo admin_url('/images/spinner.gif') ?>"/></span>
      </p>
    </form>

    <div id="app"></div>
</div>
   